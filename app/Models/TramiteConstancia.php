<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TramiteConstancia extends Model
{
    public $timestamps = false;

    protected $table = 'tramite_constancias';

    protected $fillable = [
        'expediente_movimiento_id',
        'documento_id',
        'usuario_id',
        'unidad_id',
        'tipo_acto',
        'firma_hash',
        'firma_metadata',
        'sello_institucional_id',
        'sello_imagen_path',
        'sello_texto',
        'pdf_resultante_path',
        'sello_metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'firma_metadata' => 'array',
            'sello_metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function movimiento(): BelongsTo
    {
        return $this->belongsTo(ExpedienteMovimiento::class, 'expediente_movimiento_id');
    }
}
