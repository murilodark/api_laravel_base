<?php

namespace App\Http\Requests\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class UserFilterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Permite que qualquer usuário faça essa requisição
    }

    public function rules()
    {
        return [
            'filter' => 'nullable|string|max:255', // O filtro é opcional e deve ser uma string
        ];
    }

    public function messages()
    {
        return [
            'filter.string' => 'O filtro deve ser uma string.',
            'filter.max' => 'O filtro não pode ter mais de 255 caracteres.',
        ];
    }
}
