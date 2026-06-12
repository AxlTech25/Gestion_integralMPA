<?php

namespace App\Http\Requests\Core;

use App\Rules\PasswordSegura;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50', 'unique:usuarios,username'],
            'email' => ['nullable', 'email', 'max:100'],
            'password' => ['required', 'string', new PasswordSegura()],
            'nombre_completo' => ['required', 'string', 'max:200'],
            'unidad_activa_id' => [
                'required',
                Rule::exists('unidades_organizacionales', 'id')->where('activa', true),
            ],
            'activo' => ['boolean'],
            'role_ids' => ['array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ];
    }
}
