<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SyncLog extends Model
{
    public $timestamps = false;

    protected $table = 'sync_logs';

    protected $fillable = [
        'sistema',
        'tipo_sync',
        'modo',
        'estado',
        'registros_ok',
        'registros_error',
        'mensaje',
        'ejecutado_por',
        'ejecutado_at',
    ];

    protected function casts(): array
    {
        return [
            'ejecutado_at' => 'datetime',
        ];
    }

    public function ejecutor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'ejecutado_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(SyncLogDetalle::class, 'sync_log_id');
    }
}
