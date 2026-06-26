<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NoConformidad extends Model
{
    protected $table = 'no_conformidades';

    protected $fillable = [
        'anio',
        'secuencial',
        'codigo',
        'proceso',
        'severidad',
        'descripcion',
        'requisito_incumplido',
        'evidencia',
        'contencion',
        'causa_raiz',
        'estado',
        'unidad_id',
        'reportado_por',
        'responsable_id',
        'expediente_id',
        'incidencia_id',
        'requiere_ac',
        'verificacion_eficacia',
        'verificada_por',
        'cerrada_at',
        'verificada_at',
    ];

    protected function casts(): array
    {
        return [
            'requiere_ac' => 'boolean',
            'cerrada_at' => 'datetime',
            'verificada_at' => 'datetime',
        ];
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_id');
    }

    public function reportador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'reportado_por');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsable_id');
    }

    public function verificador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'verificada_por');
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function incidencia(): BelongsTo
    {
        return $this->belongsTo(Incidencia::class, 'incidencia_id');
    }

    public function accionesCorrectivas(): HasMany
    {
        return $this->hasMany(AccionCorrectiva::class, 'no_conformidad_id');
    }

    public function accionCorrectivaActiva(): HasOne
    {
        return $this->hasOne(AccionCorrectiva::class, 'no_conformidad_id')
            ->whereNotIn('estado', ['cerrada', 'ineficaz'])
            ->latestOfMany();
    }
}
