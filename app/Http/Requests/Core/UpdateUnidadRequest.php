<?php

namespace App\Http\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'activa' => ['sometimes', 'boolean'],
            'permite_derivacion' => ['sometimes', 'boolean'],
        ];
    }
}
