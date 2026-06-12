<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiafEjecucionSnapshot extends Model
{
    public $timestamps = false;

    protected $table = 'siaf_ejecucion_snapshots';

    protected $fillable = [
        'periodo',
        'pim',
        'ejecucion_total',
        'porcentaje_ejecucion',
        'detalle_resumido_json',
        'es_simulacion',
        'sincronizado_at',
    ];

    protected function casts(): array
    {
        return [
            'pim' => 'decimal:2',
            'ejecucion_total' => 'decimal:2',
            'porcentaje_ejecucion' => 'decimal:2',
            'detalle_resumido_json' => 'array',
            'es_simulacion' => 'boolean',
            'sincronizado_at' => 'datetime',
        ];
    }
}
