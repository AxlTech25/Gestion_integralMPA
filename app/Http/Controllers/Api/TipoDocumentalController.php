<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Documentaria\StoreTipoDocumentalRequest;
use App\Http\Requests\Documentaria\UpdateTipoDocumentalRequest;
use App\Models\TipoDocumental;
use App\Models\Usuario;
use App\Services\Documentaria\TipoDocumentalService;
use Illuminate\Http\Request;

class TipoDocumentalController extends Controller
{
    public function __construct(private TipoDocumentalService $tipoService) {}

    public function index(Request $request)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json([], 401);
        }

        $query = TipoDocumental::query()
            ->with('unidadEmisora:id,codigo_org,nombre')
            ->orderBy('nombre');

        if ($request->boolean('gestion') && $usuario->hasPermiso('doc.tipos.gestionar')) {
            return response()->json($query->get());
        }

        $query->where('activo', true);

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

    public function store(StoreTipoDocumentalRequest $request)
    {
        $tipo = $this->tipoService->crear($request->validated(), $request->user());

        return response()->json($tipo, 201);
    }

    public function update(UpdateTipoDocumentalRequest $request, TipoDocumental $tipo)
    {
        $tipo = $this->tipoService->actualizar($tipo, $request->validated(), $request->user());

        return response()->json($tipo);
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
