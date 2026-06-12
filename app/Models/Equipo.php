<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Equipo extends Model
{
    protected $table = 'equipos';

    protected $fillable = [
        'codigo_patrimonial',
        'codigo_siga',
        'tipo_equipo',
        'marca',
        'modelo',
        'numero_serie',
        'estado_operativo',
        'unidad_id',
        'custodio_nombre',
        'custodio_cargo',
        'valor_patrimonial',
        'fecha_adquisicion',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'valor_patrimonial' => 'decimal:2',
            'fecha_adquisicion' => 'date',
        ];
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    public function fichaTecnica(): HasOne
    {
        return $this->hasOne(FichaTecnica::class, 'equipo_id');
    }

    public function fichasMantenimiento(): HasMany
    {
        return $this->hasMany(FichaMantenimiento::class, 'equipo_id')->orderByDesc('fecha');
    }

    public function incidencias(): HasMany
    {
        return $this->hasMany(Incidencia::class, 'equipo_id')->orderByDesc('created_at');
    }

    public function predicciones(): HasMany
    {
        return $this->hasMany(MlPrediccion::class, 'equipo_id')->orderByDesc('calculado_at');
    }

    public function ultimaPrediccion(): HasOne
    {
        return $this->hasOne(MlPrediccion::class, 'equipo_id')->latestOfMany('calculado_at');
    }
}
