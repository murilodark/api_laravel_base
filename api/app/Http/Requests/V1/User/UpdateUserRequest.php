<?php

namespace App\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Este método roda antes das rules().
     * Se o usuário não existir, ele lança a exceção que seuExceptionHandler já captura.
     */
    protected function prepareForValidation()
    {
        $id = $this->route('user') ?? $this->route('id');

        // Se o ID não existir no banco, dispara 404 imediatamente
        // O seu ApiExceptionHandler vai capturar isso e retornar seu JSON customizado
        if (!User::where('id', $id)->exists()) {
            throw new ModelNotFoundException("Usuário não encontrado.");
        }
    }

    public function rules(): array
    {
        $userId = $this->route('user') ?? $this->route('id');
        // Captura dinamicamente as chaves (perfis) do config/permissions.php
        $perfisDisponiveis = array_keys(config('permissions.roles', []));
        return [
            // Removido 'required' para permitir atualizações parciais (Patch style)
            'name'  => ['sometimes', 'string', 'min:3', 'max:255'],
            'email' => [
                'sometimes', // Permite atualizar outros campos sem reenviar o email
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => ['sometimes', 'string', 'min:8'],
            'status'   => ['sometimes', Rule::in(['ativo', 'inativo', 'pendente'])],
            'tipo'     => ['sometimes', Rule::in($perfisDisponiveis)],
        ];
    }

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



            // Status
            'status.required'   => 'O campo status é obrigatório.',
            'status.in'    => 'Status inválido. Escolha entre: ativo, inativo ou pendente.',

            // Tipo
            'tipo.required'     => 'O campo tipo é obrigatório.',
            'tipo.in'      => "Tipo de usuário inválido. Escolha entre os perfis configurados: {$perfisTexto}.",
        ];
    }
}
