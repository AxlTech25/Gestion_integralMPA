<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadOrganizacional extends Model
{
    protected $table = 'unidades_organizacionales';

    protected $fillable = [
        'codigo_org',
        'codigo_siga',
        'nombre',
        'tipo',
        'permite_derivacion',
        'gerencia_id',
        'padre_id',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'permite_derivacion' => 'boolean',
            'activa' => 'boolean',
        ];
    }

    public function gerencia(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'gerencia_id');
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'padre_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'unidad_activa_id');
    }

    public function hijos(): HasMany
    {
        return $this->hasMany(UnidadOrganizacional::class, 'padre_id');
    }

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeDestinoDerivacion($query)
    {
        return $query->activas()
            ->where('permite_derivacion', true)
            ->whereIn('tipo', ['gerencia', 'unidad', 'ejecutivo']);
    }
}

