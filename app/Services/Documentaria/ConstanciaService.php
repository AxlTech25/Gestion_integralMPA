<?php

namespace App\Services\Documentaria;

use App\Models\ExpedienteMovimiento;
use App\Models\TramiteConstancia;
use App\Models\Usuario;

class ConstanciaService
{
    public function __construct(
        private FirmaService $firmaService,
        private SelloService $selloService,
    ) {}

    public function registrarParaMovimiento(
        ExpedienteMovimiento $movimiento,
        Usuario $usuario,
        string $tipoActo,
        ?string $codigoExpediente = null,
    ): TramiteConstancia {
        $firma = $this->firmaService->firmarMovimiento($movimiento, $usuario);
        $sello = $this->selloService->resolverParaUnidad($usuario->unidad_activa_id);
        $codigo = $codigoExpediente ?? $movimiento->expediente?->codigo ?? '';

        return TramiteConstancia::create([
            'expediente_movimiento_id' => $movimiento->id,
            'usuario_id' => $usuario->id,
            'unidad_id' => $usuario->unidad_activa_id,
            'tipo_acto' => $tipoActo,
            'firma_hash' => $firma['firma_hash'],
            'firma_metadata' => $firma['firma_metadata'],
            'sello_institucional_id' => $sello?->id,
            'sello_imagen_path' => $sello?->imagen_path,
            'sello_texto' => $this->selloService->textoSello($sello, $codigo),
            'sello_metadata' => [
                'sello_nombre' => $sello?->nombre,
                'tipo_acto' => $tipoActo,
            ],
            'created_at' => now(),
        ]);
    }
}
