# HU-DOC-08 — Firma digital y sello

Registro de trazabilidad Prompt-Centered SDLC — SGMI-MPA.

| Campo | Valor |
|-------|-------|
| **ID función** | F-DOC-08 |
| **Módulo** | MOD-DOC |
| **Requisitos** | RF-DOC-12, RF-DOC-13 |
| **Estado** | Implementado |
| **Commit implementación** | `5a891bd` |
| **Ubicación principal** | `app/Services/Documentaria/FirmaService.php` |
| **Pruebas** | `tests/Feature/ExpedienteDocumentalTest.php` |
| **Prompt** | P-D-002 (`prompts/02_diseno/D-002_decision_arquitectura_v1.md`) |
| **Versión prompt** | v1 |

## Historia de usuario

Ver [../../01_requisitos/historias-usuario/HU-gestion-documentaria.md](../../01_requisitos/historias-usuario/HU-gestion-documentaria.md).

## Prompt de desarrollo

> Firma HMAC-SHA256 + sello PDF institucional

## Observaciones

ADR-004; no PKI externa

---

*El commit de trazabilidad de esta historia es el commit de Git que introduce este archivo.*
