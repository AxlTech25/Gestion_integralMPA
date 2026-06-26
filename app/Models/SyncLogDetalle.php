<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncLogDetalle extends Model
{
    public $timestamps = false;

    protected $table = 'sync_log_detalles';

    protected $fillable = [
        'sync_log_id',
        'entidad_externa',
        'referencia',
        'entidad_local',
        'entidad_local_id',
        'estado',
        'mensaje',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function syncLog(): BelongsTo
    {
        return $this->belongsTo(SyncLog::class, 'sync_log_id');
    }
}
