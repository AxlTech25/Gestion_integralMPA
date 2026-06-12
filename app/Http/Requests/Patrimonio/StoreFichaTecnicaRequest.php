<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;

class StoreFichaTecnicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cpu' => ['nullable', 'string', 'max:100'],
            'ram_gb' => ['nullable', 'integer', 'min:1', 'max:512'],
            'almacenamiento_gb' => ['nullable', 'integer', 'min:1'],
            'sistema_operativo' => ['nullable', 'string', 'max:100'],
            'red' => ['nullable', 'string', 'max:100'],
            'antiguedad_anios' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'componentes_json' => ['nullable', 'array'],
        ];
    }
}
