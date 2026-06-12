<?php

namespace App\Services\Core;

use App\Models\AuditoriaLog;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AuditoriaService
{
    public function registrar(
        string $modulo,
        string $accion,
        string $entidad,
        ?int $entidadId = null,
        ?array $metadata = null,
        ?Usuario $usuario = null,
        ?Request $request = null,
    ): AuditoriaLog {
        $request ??= request();

        return AuditoriaLog::create([
            'usuario_id' => $usuario?->id ?? auth()->id(),
            'modulo' => $modulo,
            'accion' => $accion,
            'entidad' => $entidad,
            'entidad_id' => $entidadId,
            'ip_address' => $request->ip(),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
