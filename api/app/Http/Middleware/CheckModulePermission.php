<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use App\Traits\TraitReturnJsonOlirum; // Utilizando sua trait para padronização
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: CheckModulePermission
 * 
 * Descrição: Este middleware intercepta requisições privadas para validar se o perfil 
 * do usuário possui permissão de acesso ao módulo e ao método solicitado.
 * 
 * @author Murilo Dark <contato/github se houver>
 * @date 2024-06-27 (Criado) | Atualizado para Laravel 12
 */
class CheckModulePermission
{
    use TraitReturnJsonOlirum;

    protected $authService;

    /**
     * Injeção de Dependência: O Laravel resolve o AuthService automaticamente.
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Manipula a requisição.
     * 
     * @param Request $request
     * @param Closure $next
     * @param string $modulo O nome do módulo (ex: 'users', 'vinculos') enviado via rota.
     */
    public function handle(Request $request, Closure $next, string $modulo): Response
    {
        try {
            /**
             * 1. IDENTIFICAÇÃO DA AÇÃO
             * Pega o nome do método no Controller que está sendo acessado.
             * Ex: 'index', 'store', 'vincular'.
             */
            $metodo = $request->route()->getActionMethod();

            /**
             * 2. VALIDAÇÃO DE PERMISSÃO
             * O parâmetro $modulo vem da definição da rota: 'check.permission:users' -> $modulo = 'users'.
             * O AuthService validará o Perfil do Usuário vs Módulo vs Método.
             */
            $this->authService->checkPermission($modulo, $metodo);

            // Se a permissão for válida, segue para o próximo passo (ou Controller)
            return $next($request);
            
        } catch (\Exception $e) {
            /**
             * 3. RESPOSTA DE ERRO PADRONIZADA
             * Caso o AuthService lance uma Exception (acesso negado), 
             * retornamos o erro via Trait padronizada.
             */
            return $this->ReturnJson(
                data: null,
                message: $e->getMessage(),
                status: false,
                code: 403
            );
        }
    }
}
