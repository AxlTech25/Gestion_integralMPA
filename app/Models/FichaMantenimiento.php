<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FichaMantenimiento extends Model
{
    public $timestamps = false;

    protected $table = 'fichas_mantenimiento';

    protected $fillable = [
        'equipo_id',
        'tipo',
        'fecha',
        'descripcion',
        'resultado',
        'tecnico',
        'registrado_por',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}
