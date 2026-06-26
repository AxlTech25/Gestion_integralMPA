<?php

namespace App\Http\Requests\Documentaria;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTipoDocumentalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => ['required', 'string', 'max:20', 'alpha_num', 'unique:tipos_documentales,codigo'],
            'nombre' => ['required', 'string', 'max:150'],
            'prefijo_numeracion' => ['required', 'string', 'max:20'],
            'clase_norma' => ['required', Rule::in(['acuerdo', 'decreto', 'ordenanza', 'resolucion', 'directiva', 'gestion_interna', 'otro'])],
            'ambito_emision' => ['required', Rule::in(['concejo', 'alcaldia', 'gerencia_municipal', 'gerencia', 'sub_gerencia', 'unidad'])],
            'unidad_emisora_id' => ['nullable', 'exists:unidades_organizacionales,id'],
            'registro_por_secretaria' => ['boolean'],
            'requiere_firma_antes_derivar' => ['boolean'],
            'requiere_recepcion' => ['boolean'],
            'activo' => ['boolean'],
            'unidades_registro_ids' => ['array'],
            'unidades_registro_ids.*' => ['integer', 'exists:unidades_organizacionales,id'],
        ];
    }
}
