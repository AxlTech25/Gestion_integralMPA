<?php

namespace App\Http\Requests\Calidad;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccionCorrectivaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermiso('calidad.nc.gestionar') ?? false;
    }

    public function rules(): array
    {
        return [
            'causa_raiz' => ['nullable', 'string', 'max:2000'],
            'plan_acciones' => ['required', 'string', 'min:10', 'max:4000'],
            'responsable_id' => ['nullable', 'exists:usuarios,id'],
        ];
    }
}
