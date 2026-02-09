<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\UniqueConstraintViolationException;

class ProcessarUploadClientes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $filePath,
        protected User $userSolicitante 
    ) {}

    public function handle(): void
    {
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        $path = Storage::path($this->filePath);
        $totalSucesso = 0;
        $totalErrosPerfil = 0;
        $totalErrosDuplicados = 0;

        LazyCollection::make(function () use ($path) {
            $handle = fopen($path, 'r');
            fgetcsv($handle); 

            while (($line = fgetcsv($handle, 0, ",")) !== false) {
                if (count($line) === 5) {
                    yield array_combine(['name', 'email', 'password', 'status', 'tipo'], $line);
                }
            }
            fclose($handle);
        })->each(function ($item) use (&$totalSucesso, &$totalErrosPerfil, &$totalErrosDuplicados) {
            
            // 1. Validar Perfil
            if ($item['tipo'] !== 'cliente') {
                $totalErrosPerfil++;
                return;
            }

            // 2. Tentar Inserir e Capturar Duplicidade
            try {
                User::create([
                    'name'     => $item['name'],
                    'email'    => $item['email'],
                    'password' => Hash::make($item['password']),
                    'status'   => $item['status'] ?? 'ativo',
                    'tipo'     => 'cliente',
                ]);
                $totalSucesso++;
            } catch (UniqueConstraintViolationException $e) {
                $totalErrosDuplicados++;
            } catch (\Exception $e) {
                // Outros erros genéricos
                \Illuminate\Support\Facades\Log::error("Erro ao importar linha: " . $e->getMessage());
            }
        });

        Storage::delete($this->filePath);

        $this->userSolicitante->notify(new \App\Notifications\ClientesImportadosNotification(
            $totalSucesso, 
            $totalErrosPerfil,
            $totalErrosDuplicados
        ));
    }
}
