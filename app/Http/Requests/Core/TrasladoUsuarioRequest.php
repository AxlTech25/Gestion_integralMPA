<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrasladoUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unidad_id' => [
                'required',
                Rule::exists('unidades_organizacionales', 'id')->where('activa', true),
            ],
            'motivo' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
