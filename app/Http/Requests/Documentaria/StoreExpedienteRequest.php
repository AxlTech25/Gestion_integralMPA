<?php

namespace App\Http\Requests\Documentaria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_documental_id' => ['required', 'integer', 'exists:tipos_documentales,id'],
            'asunto' => ['required', 'string', 'max:500'],
            'prioridad' => ['required', Rule::in(['baja', 'media', 'alta', 'urgente'])],
            'proveido' => ['nullable', 'string', 'max:2000'],
            'archivo' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,jpg,jpeg,png,webp',
                'mimetypes:application/pdf,image/jpeg,image/png,image/webp',
            ],
        ];
    }
}
