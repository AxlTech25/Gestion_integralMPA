<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'expediente_id',
        'version',
        'titulo',
        'es_principal',
        'documento_anterior_id',
        'archivo_path',
        'hash_contenido',
        'estado',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
        ];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'expediente_id');
    }

    public function firma(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DocumentoFirma::class, 'documento_id');
    }

    public function sello(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DocumentoSello::class, 'documento_id');
    }
}
