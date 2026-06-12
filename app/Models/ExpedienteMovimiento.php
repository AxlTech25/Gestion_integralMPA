<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExpedienteMovimiento extends Model
{
    public $timestamps = false;

    protected $table = 'expediente_movimientos';

    protected $fillable = [
        'expediente_id',
        'tipo_movimiento',
        'unidad_origen_id',
        'unidad_destino_id',
        'unidad_actuante_id',
        'usuario_id',
        'observacion',
        'proveido',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function unidadActuante(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_actuante_id');
    }

    public function unidadOrigen(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_origen_id');
    }

    public function unidadDestino(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_destino_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function constancia(): HasOne
    {
        return $this->hasOne(TramiteConstancia::class, 'expediente_movimiento_id');
    }
}
