<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Integrations\IntegracionEstadoService;
use App\Services\Integrations\SiafSyncService;
use App\Services\Integrations\SigaSyncService;
use Illuminate\Http\Request;

class IntegracionController extends Controller
{
    public function __construct(
        private IntegracionEstadoService $estadoService,
        private SigaSyncService $sigaSync,
        private SiafSyncService $siafSync,
    ) {}

    public function estado()
    {
        return response()->json($this->estadoService->estado());
    }

    public function logs(Request $request)
    {
        $limit = min(50, max(5, $request->integer('limit', 20)));

        return response()->json([
            'data' => $this->estadoService->logsRecientes($limit),
        ]);
    }

    public function syncSigaPatrimonio(Request $request)
    {
        return response()->json(
            $this->sigaSync->syncPatrimonio($request->user(), 'manual')
        );
    }

    public function syncSigaOrganigrama(Request $request)
    {
        $organigrama = $this->sigaSync->syncOrganigrama($request->user(), 'manual');
        $personal = $this->sigaSync->syncPersonal($request->user(), 'manual');

        return response()->json([
            'organigrama' => $organigrama,
            'personal' => $personal,
        ]);
    }

    public function syncSiafEjecucion(Request $request)
    {
        $periodo = $request->string('periodo')->toString() ?: null;

        return response()->json(
            $this->siafSync->syncEjecucion($request->user(), 'manual', $periodo)
        );
    }
}
