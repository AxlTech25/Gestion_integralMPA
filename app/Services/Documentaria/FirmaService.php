<?php

namespace App\Services\Documentaria;

use App\Models\Documento;
use App\Models\ExpedienteMovimiento;
use App\Models\Usuario;

class FirmaService
{
    /** @return array{firma_hash: string, firma_metadata: array} */
    public function firmarDocumento(Documento $documento, Usuario $usuario): array
    {
        $timestamp = now()->toIso8601String();
        $hashContenido = $documento->hash_contenido ?? '';
        $payload = "{$hashContenido}|{$usuario->id}|{$timestamp}";

        return [
            'firma_hash' => $this->hmac($payload),
            'firma_metadata' => [
                'tipo' => 'aplicativa',
                'usuario_id' => $usuario->id,
                'unidad_id' => $usuario->unidad_activa_id,
                'documento_id' => $documento->id,
                'timestamp' => $timestamp,
            ],
        ];
    }

    /** @return array{firma_hash: string, firma_metadata: array} */
    public function firmarMovimiento(ExpedienteMovimiento $movimiento, Usuario $usuario): array
    {
        $timestamp = now()->toIso8601String();
        $payload = "{$movimiento->expediente_id}|{$movimiento->id}|{$usuario->id}|{$timestamp}";

        return [
            'firma_hash' => $this->hmac($payload),
            'firma_metadata' => [
                'tipo' => 'constancia_movimiento',
                'movimiento_id' => $movimiento->id,
                'tipo_movimiento' => $movimiento->tipo_movimiento,
                'usuario_id' => $usuario->id,
                'unidad_id' => $usuario->unidad_activa_id,
                'timestamp' => $timestamp,
            ],
        ];
    }

    private function hmac(string $payload): string
    {
        return hash_hmac('sha256', $payload, (string) config('app.key'));
    }
}
