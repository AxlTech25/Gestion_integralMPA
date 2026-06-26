<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccionCorrectiva extends Model
{
    protected $table = 'acciones_correctivas';

    protected $fillable = [
        'anio',
        'secuencial',
        'codigo',
        'no_conformidad_id',
        'causa_raiz',
        'plan_acciones',
        'estado',
        'responsable_id',
        'evidencia_implementacion',
        'metodo_verificacion',
        'resultado_verificacion',
        'implementada_at',
        'cerrada_at',
    ];

    protected function casts(): array
    {
        return [
            'implementada_at' => 'datetime',
            'cerrada_at' => 'datetime',
        ];
    }

    public function noConformidad(): BelongsTo
    {
        return $this->belongsTo(NoConformidad::class, 'no_conformidad_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable_id');
    }
}
