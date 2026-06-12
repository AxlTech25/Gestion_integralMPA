<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditoriaLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditoriaLog::query()
            ->with('usuario:id,username,nombre_completo')
            ->orderByDesc('created_at');

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->string('modulo'));
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->string('accion'));
        }

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->integer('usuario_id'));
        }

        if ($request->filled('desde')) {
            $query->where('created_at', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $query->where('created_at', '<=', $request->date('hasta')->endOfDay());
        }

        return response()->json($query->paginate($request->integer('per_page', 25)));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = AuditoriaLog::query()
            ->with('usuario:id,username,nombre_completo')
            ->orderByDesc('created_at')
            ->limit(5000);

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->string('modulo'));
        }

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'fecha', 'usuario', 'modulo', 'accion', 'entidad', 'entidad_id', 'ip']);

            $query->chunk(200, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->created_at?->toDateTimeString(),
                        $log->usuario?->username,
                        $log->modulo,
                        $log->accion,
                        $log->entidad,
                        $log->entidad_id,
                        $log->ip_address,
                    ]);
                }
            });

            fclose($handle);
        }, 'auditoria_sgmi.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
