<?php

namespace App\Http\Requests\V1\User;

use App\Traits\TraitReturnJsonOlirum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UserDeleteRequest extends FormRequest
{
    use TraitReturnJsonOlirum;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Você pode adicionar lógica de autorização aqui, se necessário
    }

    /**
     * Captura os erros na validação
     */
    protected function failedValidation(Validator $validator)
    {
        // Retorne diretamente a resposta do ReturnJson
        throw new HttpResponseException($this->ReturnJson(
            null,
            $validator->errors()->first(),
            true,
            422
        ));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $idUserLogado = Auth::id(); // Obtém o ID do usuário logado

        return [
            'listDeleteUsers' => 'required|array', // A lista de usuários deve ser um array
            'listDeleteUsers.*.id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($idUserLogado) {
                    if ($value == $idUserLogado) {
                        $fail('Você não pode excluir sua própria conta.');
                    }
                },
            ],
        ];
    }

    // Adicione este método para personalizar as mensagens de erro
    public function messages()
    {
        return [
            'listDeleteUsers.required' => 'A lista de usuários é obrigatória.',
            'listDeleteUsers.array' => 'A lista de usuários deve ser um array.',
            'listDeleteUsers.*.id.required' => 'O ID do usuário é obrigatório.',
            'listDeleteUsers.*.id.integer' => 'O ID do usuário deve ser um número inteiro.',
            'listDeleteUsers.*.id.exists' => 'O ID do usuário deve existir na tabela de usuários.',
        ];
    }
}
