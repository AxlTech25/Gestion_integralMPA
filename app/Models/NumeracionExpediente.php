<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumeracionExpediente extends Model
{
    public $timestamps = false;

    protected $table = 'numeraciones_expediente';

    protected $fillable = [
        'tipo_documental_id',
        'anio',
        'ultimo_secuencial',
    ];

    public function tipoDocumental(): BelongsTo
    {
        return $this->belongsTo(TipoDocumental::class, 'tipo_documental_id');
    }
}
