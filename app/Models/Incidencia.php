<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidencia extends Model
{
    public $timestamps = false;

    protected $table = 'incidencias';

    protected $fillable = [
        'equipo_id',
        'reportado_por',
        'tipo',
        'descripcion',
        'estado',
        'solucion',
        'asignado_utis_id',
        'created_at',
        'cerrada_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'cerrada_at' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function reportador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'reportado_por');
    }

    public function asignadoUtis(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'asignado_utis_id');
    }
}
