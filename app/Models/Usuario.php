<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'username',
        'email',
        'password',
        'nombre_completo',
        'unidad_activa_id',
        'activo',
        'intentos_fallidos',
        'bloqueado_hasta',
        'ultimo_login',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'bloqueado_hasta' => 'datetime',
            'ultimo_login' => 'datetime',
        ];
    }

    public function unidadActiva(): BelongsTo
    {
        return $this->belongsTo(UnidadOrganizacional::class, 'unidad_activa_id');
    }
}
