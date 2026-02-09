<?php

use App\Exceptions\ApiExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use App\Http\Middleware\ForceJsonResponse;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(function () {
                    
                    // --- 🔓 ROTAS PÚBLICAS ---
                    $publicPath = base_path('routes/api/v1/public');
                    if (is_dir($publicPath)) {
                        foreach (File::allFiles($publicPath) as $file) {
                            require $file->getPathname();
                        }
                    }

                    // --- 🔐 ROTAS PRIVADAS ---
                    $privatePath = base_path('routes/api/v1/private');
                    if (is_dir($privatePath)) {
                        foreach (File::allFiles($privatePath) as $file) {
                            // Aplicamos o sanctum em cada arquivo encontrado
                            Route::middleware('auth:sanctum')->group($file->getPathname());
                        }
                    }
                });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);

        $middleware->alias([
            'check.permission' => \App\Http\Middleware\CheckModulePermission::class,
        ]);
    })
    ->withExceptions(new ApiExceptionHandler()) //trata as exceções de forma personalizada para a API
    ->withCommands([ // Registra os comandos personalizados para a API
        \App\Console\Commands\EnvioSemanalRelatorioClientes::class,
    ])
    ->create();
