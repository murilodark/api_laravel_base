<?php

/**
 * --------------------------------------------------------------------------
 * Autoload de Rotas por Versão e Contexto (Public/Private)
 * --------------------------------------------------------------------------
 * 
 * Este script automatiza o carregamento de arquivos de rota baseando-se na 
 * estrutura de diretórios. Ele elimina a necessidade de registrar manualmente 
 * cada arquivo no sistema, permitindo que a API cresça de forma modular.
 * 
 * Estrutura Esperada: routes/api/{versao}/{public|private}/*.php
 * 
 * @author Murilo Dark
 * @date 2024-06-27 (Criado) | Atualizado para Laravel 12
 */

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// Define as versões ativas da API que devem ser carregadas
$versions = ['v1']; 

foreach ($versions as $version) {

    /**
     * Grupo Raiz da Versão
     * Exemplo de URL: https://sua-api.com
     */
    Route::prefix("api/{$version}")->group(function () use ($version) {

        /**
         * 🔓 ROTAS PÚBLICAS
         * Carrega arquivos que não exigem autenticação (Ex: Login, Cadastro, Landing Page).
         * URL: /api/v1/public/seu-recurso
         */
        Route::prefix('public')->group(function () use ($version) {
           $path = base_path("routes/api/{$version}/public");

            // Verifica se o diretório existe para evitar erros de sistema
            if (is_dir($path)) {
                foreach (File::allFiles($path) as $file) {
                    // Importa o arquivo de rota dinamicamente
                    require $file->getPathname();
                }
            }
        });

        /**
         * 🔐 ROTAS PRIVADAS
         * Carrega arquivos protegidos pelo Middleware Sanctum.
         * URL: /api/v1/private/seu-recurso
         * 
         * Nota: Aqui você deve aplicar o middleware 'check.permission'
         * dentro dos arquivos individuais para maior controle granular.
         */
        Route::prefix('private')
            ->middleware('auth:sanctum') // Proteção via Token (Sanctum)
            ->group(function () use ($version) {

                $path = base_path("routes/api/{$version}/private");

                if (is_dir($path)) {
                    foreach (File::allFiles($path) as $file) {
                        // Importa o arquivo de rota dinamicamente dentro do contexto autenticado
                        require $file->getPathname();
                    }
                }
            });

    });
}
