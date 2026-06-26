<?php

namespace App\Http\Requests\Calidad;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;

class StoreNoConformidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof Usuario
            && $user->hasAnyPermiso(['calidad.nc.reportar', 'calidad.nc.gestionar']);
    }

    public function rules(): array
    {
        return [
            'proceso' => ['required', 'in:documentaria,patrimonio_ti,nucleo,indicadores,otro'],
            'severidad' => ['sometimes', 'in:leve,moderada,grave'],
            'descripcion' => ['required', 'string', 'min:10', 'max:4000'],
            'requisito_incumplido' => ['nullable', 'string', 'max:2000'],
            'evidencia' => ['nullable', 'string', 'max:2000'],
            'contencion' => ['nullable', 'string', 'max:2000'],
            'unidad_id' => ['nullable', 'exists:unidades_organizacionales,id'],
            'expediente_id' => ['nullable', 'exists:expedientes,id'],
            'incidencia_id' => ['nullable', 'exists:incidencias,id'],
        ];
    }
}
