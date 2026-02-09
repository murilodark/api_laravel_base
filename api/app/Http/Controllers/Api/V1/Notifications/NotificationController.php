<?php

namespace App\Http\Controllers\Api\V1\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Notifications\ResumoClientesNotification;

class NotificationController extends Controller
{
    /**
     * Solicitar Relatório Semanal de Clientes (BI)
     * O usuário logado pede para receber o relatório no e-mail agora.
     */
    public function solicitarResumoClientes(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // 1. Coleta os dados (Mantenha a lógica idêntica ao Command)
        $totalGeral = User::cliente()->count();
        $totalNovos = User::cliente()->where('created_at', '>=', now()->subDays(7))->count();

        // 2. Dispara a notificação
        $user->notify(new ResumoClientesNotification($totalGeral, $totalNovos));

        return $this->ReturnJson(
            null, 
            'O relatório solicitado foi gerado e enviado para o seu e-mail.', 
            true, 
            200
        );
    }

    /**
     * Disparar comandos via API (Opcional)
     * Útil para integrações ou webhooks que precisam rodar comandos artisan.
     */
    public function dispararComandoRelatorio()
    {
        // Roda o comando que já criamos anteriormente
        Artisan::call('relatorio:clientes-semanal');

        return $this->ReturnJson(
            null, 
            'Comando de relatório executado com sucesso para todos os administradores.', 
            true, 
            200
        );
    }
}
