<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlPrediccion extends Model
{
    public $timestamps = false;

    protected $table = 'ml_predicciones';

    protected $fillable = [
        'equipo_id',
        'ml_modelo_id',
        'probabilidad_falla',
        'nivel_riesgo',
        'factores_json',
        'calculado_at',
    ];

    protected function casts(): array
    {
        return [
            'probabilidad_falla' => 'decimal:4',
            'factores_json' => 'array',
            'calculado_at' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function modelo(): BelongsTo
    {
        return $this->belongsTo(MlModelo::class, 'ml_modelo_id');
    }
}
