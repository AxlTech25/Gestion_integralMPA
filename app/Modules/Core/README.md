# Módulo Core (NÚCLEO)

Núcleo transversal: autenticación, RBAC, organigrama, auditoría.

## Implementado

- Modelos: `Usuario`, `UnidadOrganizacional`, `Role`, `Permiso`, `UsuarioTraslado`, `AuditoriaLog`
- Servicios: `AuditoriaService`, `MenuService`, `UsuarioAdminService`
- API: `/api/login`, `/api/user`, `/api/menu`, `/api/usuarios`, `/api/unidades`, `/api/auditoria`
- Middleware: `permission:{codigo}`
- Frontend: `/admin/nucleo/*` (hub, usuarios, organigrama, auditoría)
- Seeders: `OrganigramaSeeder`, `RolePermisoSeeder`

Ver también módulo MOD-DOC en `app/Services/Documentaria/`.
