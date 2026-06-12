<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MlModelo extends Model
{
    public $timestamps = false;

    protected $table = 'ml_modelos';

    protected $fillable = [
        'version',
        'algoritmo',
        'parametros_json',
        'metricas_json',
        'modelo_path',
        'entrenado_at',
    ];

    protected function casts(): array
    {
        return [
            'parametros_json' => 'array',
            'metricas_json' => 'array',
            'entrenado_at' => 'datetime',
        ];
    }

    public function predicciones(): HasMany
    {
        return $this->hasMany(MlPrediccion::class, 'ml_modelo_id');
    }
}
