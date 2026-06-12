<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;

class StoreFichaMantenimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'in:preventivo,correctivo'],
            'fecha' => ['required', 'date'],
            'descripcion' => ['required', 'string', 'max:2000'],
            'resultado' => ['nullable', 'string', 'max:2000'],
            'tecnico' => ['nullable', 'string', 'max:150'],
        ];
    }
}
