<?php

namespace App\Http\Requests\Calidad;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccionCorrectivaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermiso('calidad.nc.gestionar') ?? false;
    }

    public function rules(): array
    {
        return [
            'causa_raiz' => ['nullable', 'string', 'max:2000'],
            'plan_acciones' => ['sometimes', 'string', 'min:10', 'max:4000'],
            'estado' => ['sometimes', 'in:abierta,en_implementacion,verificacion,cerrada,ineficaz'],
            'responsable_id' => ['nullable', 'exists:usuarios,id'],
            'evidencia_implementacion' => ['nullable', 'string', 'max:4000'],
            'metodo_verificacion' => ['nullable', 'string', 'max:2000'],
            'resultado_verificacion' => ['nullable', 'in:eficaz,ineficaz'],
        ];
    }
}
