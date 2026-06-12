<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expediente extends Model
{
    protected $table = 'expedientes';

    protected $fillable = [
        'tipo_documental_id',
        'anio',
        'secuencial',
        'codigo',
        'asunto',
        'prioridad',
        'unidad_origen_id',
        'unidad_actual_id',
        'estado',
        'documento_principal_id',
        'registrado_por',
        'archivado_por',
        'archivado_at',
    ];

    protected function casts(): array
    {
        return [
            'archivado_at' => 'datetime',
        ];
    }

    public function tipoDocumental(): BelongsTo
    {
        return $this->belongsTo(TipoDocumental::class, 'tipo_documental_id');
    }

    public function unidadOrigen(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_origen_id');
    }

    public function unidadActual(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_actual_id');
    }

    public function registrador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(ExpedienteMovimiento::class, 'expediente_id')->orderBy('created_at');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'expediente_id');
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(ExpedienteAdjunto::class, 'expediente_id');
    }

    public function documentoPrincipal(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_principal_id');
    }
}
