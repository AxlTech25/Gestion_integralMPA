# HU-SEC-01 — Autenticación local segura

Registro de trazabilidad Prompt-Centered SDLC — SGMI-MPA.

| Campo | Valor |
|-------|-------|
| **ID función** | F-SEC-01 |
| **Módulo** | NÚCLEO |
| **Requisitos** | RF-NC-01, RF-NC-02 |
| **Estado** | Implementado |
| **Commit implementación** | `5a891bd` |
| **Ubicación principal** | `app/Http/Controllers/Api/AuthController.php` |
| **Pruebas** | `tests/Feature/NucleoTest.php` |
| **Prompt** | P-D-001 (`prompts/02_diseno/D-001_modelo_datos_v1.md`) |
| **Versión prompt** | v1 |

## Historia de usuario

Ver [../../01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md](../../01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md).

## Prompt de desarrollo

> Implementar autenticación local con bloqueo 5 intentos y política de contraseña

## Observaciones

Login sesión; PasswordSegura; bcrypt

---

*El commit de trazabilidad de esta historia es el commit de Git que introduce este archivo.*
