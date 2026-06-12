<?php

namespace App\Services\Core;

use App\Models\UnidadOrganizacional;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UnidadAdminService
{
    public function __construct(private AuditoriaService $auditoria) {}

    public function actualizar(UnidadOrganizacional $unidad, array $data, Usuario $editor): UnidadOrganizacional
    {
        return DB::transaction(function () use ($unidad, $data, $editor) {
            $antes = [
                'activa' => $unidad->activa,
                'permite_derivacion' => $unidad->permite_derivacion,
            ];

            if (isset($data['activa'])) {
                $unidad->activa = (bool) $data['activa'];
            }

            if (isset($data['permite_derivacion'])) {
                if ($unidad->tipo === 'comite' || $unidad->tipo === 'politico') {
                    throw ValidationException::withMessages([
                        'permite_derivacion' => ['Comités y órganos políticos no pueden ser destino de derivación.'],
                    ]);
                }
                $unidad->permite_derivacion = (bool) $data['permite_derivacion'];
            }

            $unidad->save();

            $this->auditoria->registrar('NUCLEO', 'actualizar', 'unidad', $unidad->id, [
                'antes' => $antes,
                'despues' => [
                    'activa' => $unidad->activa,
                    'permite_derivacion' => $unidad->permite_derivacion,
                ],
            ], $editor);

            return $unidad->fresh(['gerencia', 'padre']);
        });
    }
}
