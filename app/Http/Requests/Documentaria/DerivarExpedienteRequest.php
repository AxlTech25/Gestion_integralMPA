<?php

namespace App\Http\Requests\Documentaria;

use Illuminate\Foundation\Http\FormRequest;

class DerivarExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'unidad_destino_id' => ['required', 'integer', 'exists:unidades_organizacionales,id'],
            'proveido' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
