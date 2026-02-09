<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\ResumoClientesNotification;

class EnvioSemanalRelatorioClientes extends Command
{
    /**
     * O nome e a assinatura do comando no terminal.
     * Ex: php artisan relatorio:clientes-semanal
     */
    protected $signature = 'relatorio:clientes-semanal';

    /**
     * Descrição que aparece na listagem do php artisan.
     */
    protected $description = 'Calcula métricas de novos clientes e envia o resumo semanal aos administradores';

    public function handle()
    {
        $this->info('📊 Iniciando processamento do relatório semanal de clientes...');

        // 1. Localiza os destinatários (Admins Ativos)
        $admins = User::admin()->ativo()->get();

        if ($admins->isEmpty()) {
            $this->warn('⚠️ Nenhum administrador ativo encontrado para receber o relatório.');
            return;
        }

        // 2. Coleta das Métricas
        $totalGeral = User::cliente()->count();
        $totalNovos = User::cliente()->where('created_at', '>=', now()->subDays(7))->count();

        // 3. Disparo das Notificações
        foreach ($admins as $admin) {
            $admin->notify(new ResumoClientesNotification($totalGeral, $totalNovos));
            $this->line("✅ Notificação enviada para: {$admin->email}");
        }

        $this->info('🚀 Relatórios enviados com sucesso!');
    }
}
