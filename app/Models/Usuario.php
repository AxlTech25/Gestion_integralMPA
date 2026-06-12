<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

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

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'usuario_role', 'usuario_id', 'role_id');
    }

    public function traslados(): HasMany
    {
        return $this->hasMany(UsuarioTraslado::class, 'usuario_id');
    }

    public function permisosCodigos(): Collection
    {
        $this->loadMissing('roles.permisos');

        return $this->roles
            ->flatMap(fn (Role $role) => $role->permisos)
            ->pluck('codigo')
            ->unique()
            ->values();
    }

    public function hasPermiso(string $codigo): bool
    {
        if ($this->roles()->where('codigo', 'ADMIN_SISTEMA')->exists()) {
            return true;
        }

        return $this->permisosCodigos()->contains($codigo);
    }

    public function hasAnyPermiso(array $codigos): bool
    {
        foreach ($codigos as $codigo) {
            if ($this->hasPermiso($codigo)) {
                return true;
            }
        }

        return false;
    }

    public function hasRole(string $codigo): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains('codigo', $codigo);
    }
}
