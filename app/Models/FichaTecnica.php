<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FichaTecnica extends Model
{
    protected $table = 'fichas_tecnicas';

    protected $fillable = [
        'equipo_id',
        'cpu',
        'ram_gb',
        'almacenamiento_gb',
        'sistema_operativo',
        'red',
        'antiguedad_anios',
        'componentes_json',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'antiguedad_anios' => 'decimal:1',
            'componentes_json' => 'array',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}
