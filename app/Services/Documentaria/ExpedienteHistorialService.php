<?php

namespace App\Services\Documentaria;

use App\Models\Expediente;
use App\Models\ExpedienteMovimiento;
use Illuminate\Support\Collection;

class ExpedienteHistorialService
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function lineaDeTiempo(Expediente $expediente): Collection
    {
        $movimientos = $expediente->movimientos->sortBy('created_at')->values();
        $hastaArchivo = $expediente->archivado_at ?? now();

        $items = $movimientos->map(function (ExpedienteMovimiento $m, int $index) use ($movimientos, $expediente, $hastaArchivo) {
            $siguiente = $movimientos[$index + 1] ?? null;
            $hasta = $siguiente?->created_at
                ?? ($expediente->estado === 'archivado' ? $hastaArchivo : now());

            $oficina = $this->oficinaTrasMovimiento($m);
            $permanenciaDias = (int) $m->created_at->diffInDays($hasta);

            return [
                'movimiento' => $m,
                'oficina_permanencia' => $oficina,
                'permanencia_dias' => $permanenciaDias,
                'permanencia_hasta' => $hasta,
                'is_current' => $index === $movimientos->count() - 1,
            ];
        });

        return $items->sortByDesc(fn ($item) => $item['movimiento']->created_at)->values();
    }

    public function oficinaTrasMovimiento(ExpedienteMovimiento $m): ?string
    {
        $m->loadMissing('unidadDestino');

        return match ($m->tipo_movimiento) {
            'registro', 'recepcion', 'derivacion', 'devolucion' => $m->unidadDestino?->nombre,
            default => null,
        };
    }

    public function formatearNodo(array $item): array
    {
        $m = $item['movimiento'];
        $isCurrent = $item['is_current'];

        $titulos = [
            'registro' => 'Registrado',
            'recepcion' => 'Recibido',
            'derivacion' => 'Derivado',
            'devolucion' => 'Devuelto',
        ];

        $constancia = $m->constancia;
        $extra = null;
        if ($constancia) {
            $extra = 'Constancia digital · '.substr($constancia->firma_hash, 0, 16).'…';
            if ($constancia->sello_texto) {
                $extra .= ' · '.$constancia->sello_texto;
            }
        }

        $permanenciaTexto = $item['permanencia_dias'] === 0
            ? 'Menos de 1 día en oficina'
            : "{$item['permanencia_dias']} ".($item['permanencia_dias'] === 1 ? 'día' : 'días').' en oficina';

        return [
            'titulo' => $titulos[$m->tipo_movimiento] ?? $m->tipo_movimiento,
            'estado' => $isCurrent ? 'ESTADO ACTUAL' : 'COMPLETADO',
            'fecha' => $m->created_at?->format('d/m/Y - H:i'),
            'descripcion' => $m->proveido ?? $m->observacion,
            'destino' => $m->unidadDestino?->nombre,
            'observacion' => $m->observacion,
            'usuario' => $m->usuario?->nombre_completo,
            'unidad' => $m->unidadActuante?->nombre,
            'oficina_permanencia' => $item['oficina_permanencia'],
            'permanencia_dias' => $item['permanencia_dias'],
            'permanencia_texto' => $permanenciaTexto,
            'icono' => match ($m->tipo_movimiento) {
                'registro' => 'inventory',
                'recepcion' => 'mail',
                'derivacion' => 'forward_to_inbox',
                'devolucion' => 'undo',
                default => 'history',
            },
            'color' => $isCurrent ? 'secondary' : 'gray',
            'extra' => $extra,
            'constancia' => $constancia ? [
                'firma_hash' => $constancia->firma_hash,
                'sello_texto' => $constancia->sello_texto,
                'tipo_acto' => $constancia->tipo_acto,
            ] : null,
        ];
    }
}
