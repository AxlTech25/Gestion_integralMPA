# P-NUCLEO — Control de accesos y seguridad del SGMI

| Campo | Valor |
|-------|-------|
| **Código** | P-NUCLEO |
| **Proceso** | Transversal — Seguridad y control de accesos |
| **Norma** | ISO 9001:2015 — 7.2, 7.5, 8.5.6 |
| **Módulo SGMI** | NÚCLEO |
| **Versión** | 1.0 |
| **Fecha** | 2026-06-11 |

---

## 1. Objetivo

Garantizar que solo personal autorizado accede al SGMI según organigrama, roles y permisos, con auditoría de operaciones críticas.

---

## 2. Política de contraseñas

- Mínimo 8 caracteres con mayúsculas, minúsculas, números y símbolos.
- Bloqueo tras 5 intentos fallidos.
- Cambio gestionado por UTIS (administración usuarios).

---

## 3. Gestión de usuarios y roles

1. UTIS crea usuarios con unidad activa y roles según organigrama.
2. Permisos por rol (`RolePermisoSeeder` — matriz documentada en requisitos).
3. Traslado de servidor entre unidades: registro en `traslados_usuario` con auditoría.

**SGMI:** `UsuariosPage`, `UnidadesPage`.

---

## 4. Roles principales (resumen)

| Rol | Uso ISO |
|-----|---------|
| OPERADOR | Operación documentaria + reporte incidencias |
| SUPERVISOR_UNIDAD | Supervisión unidad |
| GERENTE | Dashboard gerencia |
| VISTA_EJECUTIVA | Alta dirección sin operación bandeja |
| UTIS_SOPORTE | Administración sistema |
| AUDITOR_OCI | Solo lectura auditoría |
| PATRIMONIO | Registro equipos |

---

## 5. Auditoría

1. Todas las operaciones críticas registran módulo, acción, usuario, IP.
2. OCI consulta sin permiso de edición.
3. Export CSV para auditorías internas ISO.

**SGMI:** `AuditoriaPage`, `GET /api/auditoria/export`.

---

## 6. Revisión de accesos

| Actividad | Frecuencia | Responsable |
|-----------|------------|-------------|
| Revisión usuarios activos | Trimestral | UTIS |
| Revisión matriz roles/permisos | Anual | UTIS + OCI |
| Prueba acceso indebido | Anual | OCI |

---

## 7. Registros

`usuarios`, `usuario_role`, `traslados_usuario`, `auditoria_logs`.

---

## Referencias

- [HU-seguridad-y-organigrama.md](../../01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md)

---

## Control de cambios

| Versión | Fecha | Cambio |
|---------|-------|--------|
| 1.0 | 2026-06-11 | Emisión inicial |
