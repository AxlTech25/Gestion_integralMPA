<?php

namespace App\Http\Requests\Calidad;

use Illuminate\Foundation\Http\FormRequest;

class CerrarNoConformidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermiso('calidad.nc.gestionar') ?? false;
    }

    public function rules(): array
    {
        return [
            'causa_raiz' => ['nullable', 'string', 'max:2000'],
            'verificacion_eficacia' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
