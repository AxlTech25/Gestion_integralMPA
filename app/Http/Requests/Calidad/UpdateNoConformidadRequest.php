<?php

namespace App\Http\Requests\Calidad;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNoConformidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermiso('calidad.nc.gestionar') ?? false;
    }

    public function rules(): array
    {
        return [
            'proceso' => ['sometimes', 'in:documentaria,patrimonio_ti,nucleo,indicadores,otro'],
            'severidad' => ['sometimes', 'in:leve,moderada,grave'],
            'descripcion' => ['sometimes', 'string', 'min:10', 'max:4000'],
            'requisito_incumplido' => ['nullable', 'string', 'max:2000'],
            'evidencia' => ['nullable', 'string', 'max:2000'],
            'contencion' => ['nullable', 'string', 'max:2000'],
            'causa_raiz' => ['nullable', 'string', 'max:2000'],
            'estado' => ['sometimes', 'in:abierta,en_analisis,con_ac'],
            'responsable_id' => ['nullable', 'exists:usuarios,id'],
            'requiere_ac' => ['sometimes', 'boolean'],
        ];
    }
}
