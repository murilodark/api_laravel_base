<?php

namespace App\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para criação de usuário.
     */
    public function rules(): array
    {
        // Captura dinamicamente as chaves (perfis) do config/permissions.php
        $perfisDisponiveis = array_keys(config('permissions.roles', []));
        return [
            'name'     => ['required', 'string', 'min:3', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'status' => ['sometimes', Rule::in(['ativo', 'inativo', 'pendente'])],
            'tipo'   => ['sometimes', Rule::in($perfisDisponiveis)], // Validação dinâmica
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        // Gera a lista de perfis para a mensagem de erro dinamicamente
        $perfisTexto = implode(', ', array_keys(config('permissions.roles', [])));

        return [
            // Nome
            'name.required'     => 'O campo nome é obrigatório.',
            'name.string'       => 'O nome deve ser um texto válido.',
            'name.min'          => 'O nome deve ter pelo menos 3 caracteres.',
            'name.max'          => 'O nome não pode ultrapassar 255 caracteres.',

            // E-mail
            'email.required'    => 'O campo e-mail é obrigatório.',
            'email.email'       => 'Informe um endereço de e-mail válido.',
            'email.max'         => 'O e-mail não pode ultrapassar 255 caracteres.',
            'email.unique'      => 'Este e-mail já está cadastrado em nossa base de dados.',

            // Senha
            'password.required' => 'O campo senha é obrigatório.',
            'password.min'      => 'A senha deve ter no mínimo 6 caracteres.',

            // Status
            'status.required'   => 'O campo status é obrigatório.',
            'status.in'    => 'Status inválido. Escolha entre: ativo, inativo ou pendente.',

            // Tipo
            'tipo.required'     => 'O campo tipo é obrigatório.',
            'tipo.in'      => "Tipo de usuário inválido. Escolha entre os perfis configurados: {$perfisTexto}.",
        ];
    }
}
