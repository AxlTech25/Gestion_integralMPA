<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpedienteAdjunto extends Model
{
    public $timestamps = false;

    protected $table = 'expediente_adjuntos';

    protected $fillable = [
        'expediente_id',
        'nombre_archivo',
        'path',
        'mime_type',
        'tamano_bytes',
        'hash_archivo',
        'subido_por',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }
}
