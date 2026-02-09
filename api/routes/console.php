<?php

use Illuminate\Support\Facades\Schedule;

/**
 * --------------------------------------------------------------------------
 * Agendamento de Tarefas (Console/CLI)
 * --------------------------------------------------------------------------
 * 
 * Aqui é onde definimos a frequência de execução dos comandos customizados.
 * O Laravel Task Scheduler centraliza todas as rotas de cron da aplicação.
 */

// Agenda o envio do relatório de crescimento da base de clientes
// Executa toda segunda-feira às 08:00 da manhã
Schedule::command('relatorio:clientes-semanal')->weeklyOn(1, '08:00');

// Caso precise testar o agendamento localmente, você pode usar:
// php artisan schedule:run
