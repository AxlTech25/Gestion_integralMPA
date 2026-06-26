<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonalSigaReferencia extends Model
{
    protected $table = 'personal_siga_referencias';

    protected $fillable = [
        'codigo_siga',
        'dni',
        'nombre_completo',
        'unidad_id',
        'activo_siga',
        'usuario_id',
        'desactivacion_sugerida',
        'sincronizado_at',
    ];

    protected function casts(): array
    {
        return [
            'activo_siga' => 'boolean',
            'desactivacion_sugerida' => 'boolean',
            'sincronizado_at' => 'datetime',
        ];
    }

    public function unidad(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
