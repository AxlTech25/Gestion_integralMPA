<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoSello extends Model
{
    public $timestamps = false;

    protected $table = 'documento_sellos';

    protected $fillable = [
        'documento_id',
        'sello_institucional_id',
        'sello_imagen_path',
        'sello_metadata',
        'aplicado_at',
    ];

    protected function casts(): array
    {
        return [
            'sello_metadata' => 'array',
            'aplicado_at' => 'datetime',
        ];
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
}
