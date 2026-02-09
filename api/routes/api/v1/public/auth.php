<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Grupo de versão 1 da API
Route::prefix('v1')->group(function () {

    // Rota pública de login
    Route::post('autentitcacao', [AuthController::class, 'login']);
});
