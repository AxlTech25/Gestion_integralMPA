<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoDocumental;
use App\Models\Usuario;
use Illuminate\Http\Request;

class TipoDocumentalController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json([], 401);
        }

        $query = TipoDocumental::query()
            ->with('unidadEmisora:id,codigo_org,nombre')
            ->where('activo', true)
            ->orderBy('nombre');

        if (! $usuario->hasPermiso('doc.tipos.gestionar')) {
            $unidadId = $usuario->unidad_activa_id;
            $query->where(function ($q) use ($unidadId, $usuario) {
                $q->where('unidad_emisora_id', $unidadId)
                    ->orWhereHas('unidadesRegistro', fn ($r) => $r->where('unidad_id', $unidadId));

                if ($usuario->hasPermiso('doc.expediente.registrar')) {
                    $q->orWhere('registro_por_secretaria', true);
                }
            });
        }

        return response()->json($query->get());
    }

    public function previewCodigo(Request $request, TipoDocumental $tipo)
    {
        $anio = (int) $request->integer('anio', now()->year);
        $ultimo = $tipo->expedientes()
            ->where('anio', $anio)
            ->max('secuencial') ?? 0;

        $secuencial = $ultimo + 1;
        $codigo = str_replace(
            ['{prefijo}', '{anio}', '{secuencial}'],
            [$tipo->prefijo_numeracion, $anio, str_pad((string) $secuencial, 4, '0', STR_PAD_LEFT)],
            $tipo->formato_display
        );

        return response()->json([
            'codigo_preview' => $codigo,
            'anio' => $anio,
            'secuencial' => $secuencial,
        ]);
    }
}
