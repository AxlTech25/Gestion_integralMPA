<?php

namespace App\Http\Requests\Patrimonio;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_patrimonial' => ['required', 'string', 'max:50', 'unique:equipos,codigo_patrimonial'],
            'codigo_siga' => ['nullable', 'string', 'max:50'],
            'tipo_equipo' => ['required', 'in:pc,servidor,impresora,red,otro'],
            'marca' => ['required', 'string', 'max:100'],
            'modelo' => ['required', 'string', 'max:100'],
            'numero_serie' => ['nullable', 'string', 'max:100'],
            'estado_operativo' => ['sometimes', 'in:operativo,reparacion,baja,almacen'],
            'unidad_id' => ['required', 'exists:unidades_organizacionales,id'],
            'custodio_nombre' => ['nullable', 'string', 'max:200'],
            'custodio_cargo' => ['nullable', 'string', 'max:150'],
            'valor_patrimonial' => ['nullable', 'numeric', 'min:0'],
            'fecha_adquisicion' => ['nullable', 'date'],
        ];
    }
}
