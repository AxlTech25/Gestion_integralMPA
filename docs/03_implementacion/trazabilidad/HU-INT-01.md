# HU-INT-01 — Importación patrimonio SIGA vía API

Registro de trazabilidad Prompt-Centered SDLC — SGMI-MPA.

| Campo | Valor |
|-------|-------|
| **ID función** | F-INT-01 |
| **Módulo** | INT |
| **Requisitos** | RI-SIGA-01, RI-SIGA-02, RI-SIGA-04 |
| **Estado** | Implementado |
| **Commit implementación** | `71144a5` |
| **Ubicación principal** | `app/Services/Integrations/SigaSyncService.php` |
| **Pruebas** | `tests/Feature/IntegracionTest.php` |
| **Prompt** | P-D-002 (`prompts/02_diseno/D-002_decision_arquitectura_v1.md`) |
| **Versión prompt** | v1 |

## Historia de usuario

Ver [../../01_requisitos/historias-usuario/HU-integraciones.md](../../01_requisitos/historias-usuario/HU-integraciones.md).

## Prompt de desarrollo

> Sync diario y manual patrimonio SIGA; upsert codigo_siga

## Observaciones

SyncSigaPatrimonioJob; IntegracionesPage.vue

---

*El commit de trazabilidad de esta historia es el commit de Git que introduce este archivo.*
