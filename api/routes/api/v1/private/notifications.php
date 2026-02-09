<?php

use App\Http\Controllers\Api\V1\Notifications\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1/notifications')->group(function () {

    // Grupo de Permissão: notifications
    Route::middleware('check.permission:notifications')->group(function () {

        // Solicitar relatório de crescimento (BI)
        Route::post('/solicitar-resumo', [NotificationController::class, 'solicitarResumoClientes']);
    });
});
