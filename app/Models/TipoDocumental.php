<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoDocumental extends Model
{
    protected $table = 'tipos_documentales';

    protected $fillable = [
        'codigo',
        'nombre',
        'prefijo_numeracion',
        'formato_display',
        'clase_norma',
        'ambito_emision',
        'unidad_emisora_id',
        'registro_por_secretaria',
        'requiere_firma_antes_derivar',
        'requiere_recepcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'registro_por_secretaria' => 'boolean',
            'requiere_firma_antes_derivar' => 'boolean',
            'requiere_recepcion' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function unidadEmisora(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_emisora_id');
    }

    public function expedientes(): HasMany
    {
        return $this->hasMany(Expediente::class, 'tipo_documental_id');
    }

    public function unidadesRegistro(): BelongsToMany
    {
        return $this->belongsToMany(
            UnidadOrganizacional::class,
            'tipo_documental_unidades_registro',
            'tipo_documental_id',
            'unidad_id'
        );
    }
}
