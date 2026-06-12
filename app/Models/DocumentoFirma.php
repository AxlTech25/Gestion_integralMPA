<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoFirma extends Model
{
    public $timestamps = false;

    protected $table = 'documento_firmas';

    protected $fillable = [
        'documento_id',
        'usuario_id',
        'unidad_id',
        'firma_hash',
        'firma_metadata',
        'firmado_at',
    ];

    protected function casts(): array
    {
        return [
            'firma_metadata' => 'array',
            'firmado_at' => 'datetime',
        ];
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
}
