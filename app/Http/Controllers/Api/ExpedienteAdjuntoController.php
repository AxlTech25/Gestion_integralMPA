<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpedienteAdjunto;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpedienteAdjuntoController extends Controller
{
    public function download(Request $request, ExpedienteAdjunto $adjunto)
    {
        $usuario = $request->user();
        if (! $usuario instanceof Usuario) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        if (! Storage::disk('local')->exists($adjunto->path)) {
            abort(404, 'Archivo no encontrado.');
        }

        return Storage::disk('local')->download(
            $adjunto->path,
            $adjunto->nombre_archivo,
            ['Content-Type' => $adjunto->mime_type]
        );
    }
}
