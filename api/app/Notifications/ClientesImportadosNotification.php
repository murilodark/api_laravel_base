<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientesImportadosNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected int $totalSucesso, 
        protected int $totalErrosPerfil,
        protected int $totalErrosDuplicados
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Relatório de Importação de Clientes')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O processamento da sua planilha de clientes foi concluído. Abaixo você confere o balanço detalhado da operação:')
            ->line("✅ **{$this->totalSucesso}** registros processados com sucesso.");

        if ($this->totalErrosPerfil > 0) {
            $message->line("⚠️ **{$this->totalErrosPerfil}** registros ignorados por possuírem perfis divergentes de 'cliente'.");
        }

        if ($this->totalErrosDuplicados > 0) {
            $message->line("🚫 **{$this->totalErrosDuplicados}** registros não processados por e-mails já existentes na base.");
        }

        if ($this->totalErrosPerfil > 0 || $this->totalErrosDuplicados > 0) {
            $message->line('---')
                ->line('**Orientações para reenvio:**')
                ->line('Para os registros não processados, sugerimos que valide em sua planilha se os e-mails já estão cadastrados ou se os perfis estão definidos corretamente como "cliente". Após os ajustes, você poderá reenviar apenas as linhas corrigidas para processamento.');
        } else {
            $message->line('Parabéns! Todos os dados estavam em conformidade e foram integrados perfeitamente.');
        }

        return $message->action('Acessar Painel de Usuários', url('/'))
                       ->line('Obrigado por utilizar a estrutura API Olirum!');
    }
}
