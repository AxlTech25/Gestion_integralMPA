<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Documento;
use App\Models\Usuario;
use App\Services\Documentaria\DocumentoService;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function __construct(private DocumentoService $documentoService) {}

    public function firmar(Request $request, Documento $documento)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $documento = $this->documentoService->firmar($documento, $usuario);
        $documento->load('expediente');

        return response()->json([
            'id' => $documento->id,
            'estado' => $documento->estado,
            'titulo' => $documento->titulo,
            'expediente_codigo' => $documento->expediente->codigo,
        ]);
    }
}
