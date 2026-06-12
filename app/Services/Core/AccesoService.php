<?php

namespace App\Services\Core;

use App\Models\Usuario;

class AccesoService
{
    /** Permisos que habilitan bandeja, derivación y operación documentaria. */
    private const PERMISOS_OPERACION_DOCUMENTARIA = [
        'doc.expediente.registrar',
        'doc.expediente.derivar',
        'doc.expediente.recepcionar',
        'doc.expediente.devolver',
        'doc.expediente.archivar',
        'doc.documento.firmar',
    ];

    public function puedeOperarDocumentaria(Usuario $usuario): bool
    {
        return $usuario->hasAnyPermiso(self::PERMISOS_OPERACION_DOCUMENTARIA);
    }

    public function esVistaEjecutiva(Usuario $usuario): bool
    {
        $usuario->loadMissing('roles');

        if (! $usuario->roles->contains('codigo', 'VISTA_EJECUTIVA')) {
            return false;
        }

        return ! $this->puedeOperarDocumentaria($usuario);
    }

    public function puedeConsultarExpedientes(Usuario $usuario): bool
    {
        return $usuario->hasPermiso('doc.expediente.consultar');
    }
}
