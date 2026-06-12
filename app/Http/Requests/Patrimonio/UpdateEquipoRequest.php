<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_patrimonial' => ['sometimes', 'string', 'max:50'],
            'codigo_siga' => ['nullable', 'string', 'max:50'],
            'tipo_equipo' => ['sometimes', 'in:pc,servidor,impresora,red,otro'],
            'marca' => ['sometimes', 'string', 'max:100'],
            'modelo' => ['sometimes', 'string', 'max:100'],
            'numero_serie' => ['nullable', 'string', 'max:100'],
            'estado_operativo' => ['sometimes', 'in:operativo,reparacion,baja,almacen'],
            'unidad_id' => ['sometimes', 'exists:unidades_organizacionales,id'],
            'custodio_nombre' => ['nullable', 'string', 'max:200'],
            'custodio_cargo' => ['nullable', 'string', 'max:150'],
            'valor_patrimonial' => ['nullable', 'numeric', 'min:0'],
            'fecha_adquisicion' => ['nullable', 'date'],
        ];
    }
}
