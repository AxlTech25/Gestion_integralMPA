# HU-SEC-04 — Consulta auditoría OCI

Registro de trazabilidad Prompt-Centered SDLC — SGMI-MPA.

| Campo | Valor |
|-------|-------|
| **ID función** | F-SEC-04 |
| **Módulo** | NÚCLEO |
| **Requisitos** | RF-NC-06 |
| **Estado** | Implementado |
| **Commit implementación** | `5a891bd` |
| **Ubicación principal** | `app/Http/Controllers/Api/AuditoriaController.php` |
| **Pruebas** | `tests/Feature/NucleoTest.php` |
| **Prompt** | P-D-001 (`prompts/02_diseno/D-001_modelo_datos_v1.md`) |
| **Versión prompt** | v1 |

## Historia de usuario

Ver [../../01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md](../../01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md).

## Prompt de desarrollo

> Consulta paginada solo lectura para rol AUDITOR_OCI

## Observaciones

AuditoriaPage.vue; export CSV

---

*El commit de trazabilidad de esta historia es el commit de Git que introduce este archivo.*
