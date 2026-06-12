<?php

namespace App\Http\Requests\Core;

use App\Rules\PasswordSegura;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario');

        return [
            'email' => ['nullable', 'email', 'max:100'],
            'password' => ['nullable', 'string', new PasswordSegura()],
            'nombre_completo' => ['sometimes', 'string', 'max:200'],
            'activo' => ['boolean'],
            'role_ids' => ['array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }
}
