<?php

namespace App\Http\Requests\Patrimonio;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof Usuario
            && $user->hasAnyPermiso(['pat.incidencia.reportar', 'pat.incidencia.gestionar']);
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
