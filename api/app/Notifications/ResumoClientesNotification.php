<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResumoClientesNotification extends Notification
{
    use Queueable;

    /**
     * @param int $totalGeral Total histórico de clientes na base
     * @param int $totalNovos Total de clientes cadastrados nos últimos 7 dias
     */
    public function __construct(
        protected int $totalGeral, 
        protected int $totalNovos
    ) {}

    public function via($notifiable) 
    { 
        return ['mail']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Relatório Semanal: Crescimento da Base de Clientes')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Acompanhe o desempenho da sua plataforma. Aqui está o balanço de crescimento da sua base de clientes dos últimos 7 dias:')
            ->line("📊 **Total acumulado de clientes:** {$this->totalGeral}")
            ->line("📈 **Novos cadastros nos últimos 7 dias:** {$this->totalNovos}")
            ->line($this->getMensagemEngajamento())
            ->action('Gerenciar Clientes', url('/api/v1/users'))
            ->line('Continue acompanhando seus indicadores para expandir seus resultados.')
            ->line('Obrigado por utilizar o sistema Olirum!');
    }

    /**
     * Gera uma frase dinâmica baseada no volume de novos clientes.
     */
    protected function getMensagemEngajamento(): string
    {
        if ($this->totalNovos === 0) {
            return 'Nesta última semana não registramos novos clientes. Que tal planejar uma nova campanha de engajamento?';
        }

        if ($this->totalNovos > 50) {
            return 'Sua base está crescendo em um ritmo acelerado! Excelente trabalho na captação.';
        }

        return 'Sua base de clientes continua em constante expansão.';
    }
}
