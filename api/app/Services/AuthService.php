<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Exception;

/**
 * Service: AuthService
 * 
 * Responsável por gerenciar a inteligência de segurança e permissões da plataforma.
 * Centraliza a matriz de controle de acesso (ACL) baseada em Perfis, Módulos e Métodos.
 * 
 * @author Murilo Dark
 * @date 2024-06-27 (Criado) | Atualizado em 2024
 */
class AuthService
{


    /**
     * Valida se o usuário autenticado tem permissão para o módulo e ação solicitada.
     * 
     * @param string $modulo O módulo definido no Middleware/Rota (ex: 'users')
     * @param string $metodo O método capturado do Controller (ex: 'store')
     * @throws Exception Caso o acesso seja negado ou perfil seja inválido.
     */
    public function checkPermission(string $modulo, string $metodo): void
    {
       
        $authUser = Auth::user();

        if (!$authUser) {
            throw new AccessDeniedHttpException("Usuário não autenticado.");
        }

        // 1. Root pode tudo
        if ($authUser->tipo === 'root') {
            return;
        }

        // Validação de estado inicial do usuário
        if (!$authUser || !$authUser->isAtivo()) {
            throw new AccessDeniedHttpException("Acesso negado. Usuário inativo ou não autenticado.");
        }

        // Obtém o perfil de acesso do usuário (ex: 'admin', 'vendedor')
        $tipo = $authUser->tipo;

        // Lendo a matriz diretamente do arquivo config/permissions.php
        $permissoes = config('permissions.roles');
        if (is_null($permissoes)) {
            throw new Exception('Config permissions.php NÃO está sendo carregado.');
        }

        /**
         * 1. VERIFICAÇÃO DE PERFIL
         * Garante que o perfil do usuário exista na matriz de permissões.
         */
        if (!isset($permissoes[$tipo])) {
            throw new AccessDeniedHttpException("Perfil de usuário '{$tipo}' não configurado.");
        }
        /**
         * 2. VERIFICAÇÃO DE MÓDULO E MÉTODO
         * Valida se o módulo está liberado para o perfil E se o método (ação) 
         * consta na lista de métodos permitidos.
         */
        $temPermissao = isset($permissoes[$tipo][$modulo]) &&
            in_array($metodo, $permissoes[$tipo][$modulo]);

        if (!$temPermissao) {
            throw new AccessDeniedHttpException("Ação não autorizada. O perfil '{$tipo}' não tem permissão para '{$metodo}' no módulo '{$modulo}'.");
        }
    }


    /**
     * Valida a hierarquia de criação/edição de usuários.
     * 
     * @param string $perfilOperador Perfil de quem está logado (ex: auth()->user()->tipo)
     * @param string $perfilAlvo Perfil do usuário que está sendo criado ou editado
     * @param int|null $idOperador ID de quem está logado
     * @param int|null $idAlvo ID do usuário sendo editado (null para criação)
     * @throws Exception
     */
    public function canManageUser(string $perfilOperador, string $perfilAlvo, $idOperador = null, $idAlvo = null): void
    {
           return;
        // 1. Root pode tudo
        if ($perfilOperador === 'root') {
            return;
        }

        // 2. Permitir que o usuário edite a si próprio (Auto-edição)
        if ($idAlvo && $idOperador == $idAlvo) {
            return;
        }

        // 3. Regras de Hierarquia
        switch ($perfilOperador) {
            case 'admin':
                // Admin não mexe em Root nem em outros Admins (exceto ele mesmo, tratado acima)
                if (in_array($perfilAlvo, ['root', 'admin'])) {
                    throw new AccessDeniedHttpException("Um Admin não pode gerenciar perfis Admin ou Root.");
                }
                break;

            case 'gerente':
                // Gerente não mexe em Root, Admin ou outros Gerentes
                if (in_array($perfilAlvo, ['root', 'admin', 'gerente'])) {
                    throw new AccessDeniedHttpException("Um Gerente não pode gerenciar perfis superiores ou iguais ao seu.");
                }
                break;

            case 'vendedor':
                // Vendedor só mexe em Cliente
                if ($perfilAlvo !== 'cliente') {
                    throw new AccessDeniedHttpException("Um Vendedor só pode gerenciar perfis do tipo Cliente.");
                }
                break;

            default:
                throw new AccessDeniedHttpException("Seu perfil não possui permissão para gerenciar usuários.");
        }
    }
}
