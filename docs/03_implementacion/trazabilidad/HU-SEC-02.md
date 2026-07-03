# HU-SEC-02 — Permisos por rol y organigrama

Registro de trazabilidad Prompt-Centered SDLC — SGMI-MPA.

| Campo | Valor |
|-------|-------|
| **ID función** | F-SEC-02 |
| **Módulo** | NÚCLEO |
| **Requisitos** | RF-NC-03, RF-NC-04, RF-NC-10 |
| **Estado** | Implementado |
| **Commit implementación** | `5a891bd` |
| **Ubicación principal** | `app/Services/Core/AccesoService.php` |
| **Pruebas** | `tests/Feature/NucleoTest.php` |
| **Prompt** | P-D-001 (`prompts/02_diseno/D-001_modelo_datos_v1.md`) |
| **Versión prompt** | v1 |

## Historia de usuario

Ver [../../01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md](../../01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md).

## Prompt de desarrollo

> RBAC por organigrama; rol VISTA_EJECUTIVA; menú por permisos

## Observaciones

EnsurePermission middleware; MenuService

---

*El commit de trazabilidad de esta historia es el commit de Git que introduce este archivo.*
