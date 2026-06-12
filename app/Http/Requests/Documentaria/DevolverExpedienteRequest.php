<?php

namespace App\Http\Requests\Documentaria;

use Illuminate\Foundation\Http\FormRequest;

class DevolverExpedienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'observacion' => ['required', 'string', 'min:3', 'max:2000'],
        ];
    }
}
