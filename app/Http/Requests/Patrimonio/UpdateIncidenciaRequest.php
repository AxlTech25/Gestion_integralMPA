<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', 'in:abierta,en_atencion,cerrada'],
            'solucion' => ['required_if:estado,cerrada', 'nullable', 'string', 'max:2000'],
            'estado_operativo_equipo' => ['nullable', 'in:operativo,reparacion,baja,almacen'],
        ];
    }
}
