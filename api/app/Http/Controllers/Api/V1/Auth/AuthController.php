<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller; // Este é o seu controller principal
use App\Http\Requests\V1\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    /**
     * Login com Rate Limit e Verificação de Status
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $key = 'login:' . strtolower($credentials['email']) . '|' . $request->ip();

        // 1. Verifica se há excesso de tentativas (Proteção contra Brute Force)
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return $this->ReturnJson(
                ['retry_after' => $seconds],
                "Muitas tentativas. Tente novamente em {$seconds} segundos.",
                false,
                429
            );
        }

        // 2. Tenta autenticar
        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($key, 60); // Registra a falha por 60 segundos
            return $this->ReturnJson(null, 'Credenciais inválidas.', false, 401);
        }

        $user = Auth::user();

        // 3. Verifica se o usuário está ativo (Usando a lógica dos seus Requests)
        if (isset($user->status) && $user->status !== 'ativo') {
            Auth::logout();
            return $this->ReturnJson(null, 'Sua conta está inativa.', false, 403);
        }

        // 4. Sucesso: Limpa o limitador e gera o token Sanctum
        RateLimiter::clear($key);
        $user = Auth::user();
        if ($user instanceof \App\Models\User) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return $this->ReturnJson(
            [
                'user'  => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'tipo'  => $user->tipo,
                ],
                'token' => $token
            ],
            'Login realizado com sucesso.'
        );
    }

    /**
     * Logout - Invalida o token atual
     */
    public function logout(Request $request)
    {
        // currentAccessToken() garante que apenas o token desta sessão seja removido
        $request->user()->currentAccessToken()->delete();

        return $this->ReturnJson(null, 'Logout realizado com sucesso.');
    }

    /**
     * Me - Retorna os dados do usuário logado filtrados
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return $this->ReturnJson(
            [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'tipo'  => $user->tipo,
                'status' => $user->status
            ],
            'Dados do usuário autenticado.'
        );
    }
}
