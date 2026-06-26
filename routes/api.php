<?php

use App\Http\Controllers\Api\IntegracionController;
use App\Http\Controllers\Api\AuditoriaController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentoController;
use App\Http\Controllers\Api\ExpedienteAdjuntoController;
use App\Http\Controllers\Api\EquipoController;
use App\Http\Controllers\Api\ExpedienteController;
use App\Http\Controllers\Api\FichaController;
use App\Http\Controllers\Api\IncidenciaController;
use App\Http\Controllers\Api\MlPrediccionController;
use App\Http\Controllers\Api\NoConformidadController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\TipoDocumentalController;
use App\Http\Controllers\Api\UnidadController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/menu', [MenuController::class, 'index']);

    Route::get('/unidades/tree', [UnidadController::class, 'tree']);
    Route::get('/unidades/derivacion', [UnidadController::class, 'derivacion']);
    Route::get('/unidades', [UnidadController::class, 'index']);
    Route::get('/unidades/{unidad}', [UnidadController::class, 'show']);

    Route::middleware('permission:core.usuarios.gestionar')->group(function () {
        Route::get('/roles', [UsuarioController::class, 'roles']);
        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show']);
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
        Route::post('/usuarios/{usuario}/traslado', [UsuarioController::class, 'traslado']);
        Route::put('/unidades/{unidad}', [UnidadController::class, 'update']);
    });

    Route::middleware('permission:core.auditoria.consultar')->group(function () {
        Route::get('/auditoria', [AuditoriaController::class, 'index']);
        Route::get('/auditoria/export', [AuditoriaController::class, 'export']);
    });

    Route::middleware('permission:doc.tipos.gestionar')->group(function () {
        Route::post('/tipos-documentales', [TipoDocumentalController::class, 'store']);
        Route::put('/tipos-documentales/{tipo}', [TipoDocumentalController::class, 'update']);
    });

    Route::middleware('permission:doc.expediente.consultar')->group(function () {
        Route::get('/tipos-documentales', [TipoDocumentalController::class, 'index']);
        Route::get('/tipos-documentales/{tipo}/preview-codigo', [TipoDocumentalController::class, 'previewCodigo']);
        Route::get('/expedientes/buscar', [ExpedienteController::class, 'buscar']);
        Route::get('/expedientes/bandeja', [ExpedienteController::class, 'bandeja']);
        Route::get('/expedientes/codigo/{codigo}', [ExpedienteController::class, 'showByCodigo']);
        Route::get('/expedientes/adjuntos/{adjunto}/download', [ExpedienteAdjuntoController::class, 'download']);
        Route::get('/expedientes/{expediente}', [ExpedienteController::class, 'show']);
    });

    Route::middleware('permission:doc.expediente.registrar')->group(function () {
        Route::post('/expedientes', [ExpedienteController::class, 'store']);
    });

    Route::middleware('permission:doc.expediente.derivar')->group(function () {
        Route::post('/expedientes/{expediente}/derivar', [ExpedienteController::class, 'derivar']);
    });

    Route::middleware('permission:doc.expediente.recepcionar')->group(function () {
        Route::post('/expedientes/{expediente}/recepcionar', [ExpedienteController::class, 'recepcionar']);
    });

    Route::middleware('permission:doc.expediente.devolver')->group(function () {
        Route::post('/expedientes/{expediente}/devolver', [ExpedienteController::class, 'devolver']);
    });

    Route::middleware('permission:doc.expediente.archivar')->group(function () {
        Route::post('/expedientes/{expediente}/archivar', [ExpedienteController::class, 'archivar']);
    });

    Route::middleware('permission:doc.documento.firmar')->group(function () {
        Route::post('/documentos/{documento}/firmar', [DocumentoController::class, 'firmar']);
    });

    Route::middleware('permission:pat.equipo.consultar')->group(function () {
        Route::get('/equipos', [EquipoController::class, 'index']);
        Route::get('/equipos/buscar-soporte', [EquipoController::class, 'buscarSoporte']);
        Route::get('/equipos/{equipo}', [EquipoController::class, 'show']);
        Route::get('/ml/semaforo', [MlPrediccionController::class, 'semaforo']);
        Route::get('/ml/criticos', [MlPrediccionController::class, 'criticos']);
    });

    Route::get('/incidencias', [IncidenciaController::class, 'index']);
    Route::post('/incidencias', [IncidenciaController::class, 'store']);
    Route::put('/incidencias/{incidencia}', [IncidenciaController::class, 'update'])
        ->middleware('permission:pat.incidencia.gestionar');

    Route::middleware('permission:pat.equipo.registrar')->group(function () {
        Route::post('/equipos', [EquipoController::class, 'store']);
        Route::put('/equipos/{equipo}', [EquipoController::class, 'update']);
    });

    Route::middleware('permission:pat.ficha.gestionar')->group(function () {
        Route::post('/equipos/{equipo}/ficha-tecnica', [FichaController::class, 'storeTecnica']);
        Route::post('/equipos/{equipo}/mantenimiento', [FichaController::class, 'storeMantenimiento']);
        Route::post('/ml/ejecutar', [MlPrediccionController::class, 'ejecutar']);
    });

    Route::get('/dashboard/operativo', [DashboardController::class, 'operativo']);

    Route::middleware('permission:dash.estrategico.ver')->group(function () {
        Route::get('/dashboard/estrategico', [DashboardController::class, 'estrategico']);
    });

    Route::get('/calidad/resumen', [NoConformidadController::class, 'resumen']);
    Route::get('/no-conformidades', [NoConformidadController::class, 'index']);
    Route::get('/no-conformidades/{noConformidad}', [NoConformidadController::class, 'show']);
    Route::post('/no-conformidades', [NoConformidadController::class, 'store']);
    Route::put('/no-conformidades/{noConformidad}', [NoConformidadController::class, 'update'])
        ->middleware('permission:calidad.nc.gestionar');
    Route::post('/no-conformidades/{noConformidad}/cerrar', [NoConformidadController::class, 'cerrar'])
        ->middleware('permission:calidad.nc.gestionar');
    Route::post('/no-conformidades/{noConformidad}/acciones-correctivas', [NoConformidadController::class, 'storeAccionCorrectiva'])
        ->middleware('permission:calidad.nc.gestionar');
    Route::put('/acciones-correctivas/{accionCorrectiva}', [NoConformidadController::class, 'updateAccionCorrectiva'])
        ->middleware('permission:calidad.nc.gestionar');

    Route::middleware('permission:int.sync.ejecutar')->prefix('integraciones')->group(function () {
        Route::get('/estado', [IntegracionController::class, 'estado']);
        Route::get('/sync-logs', [IntegracionController::class, 'logs']);
        Route::post('/siga/patrimonio', [IntegracionController::class, 'syncSigaPatrimonio']);
        Route::post('/siga/organigrama', [IntegracionController::class, 'syncSigaOrganigrama']);
        Route::post('/siaf/ejecucion', [IntegracionController::class, 'syncSiafEjecucion']);
    });
});
