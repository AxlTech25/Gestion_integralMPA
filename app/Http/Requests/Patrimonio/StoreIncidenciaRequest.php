<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'equipo_id' => ['required', 'exists:equipos,id'],
            'tipo' => ['required', 'in:falla,averia,requerimiento'],
            'descripcion' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
