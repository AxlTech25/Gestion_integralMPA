# Fase de diseño — SGMI

**Versión:** 2.0  
**Entrada:** Requisitos v1.1 + [decisiones-confirmadas.md](../01_requisitos/decisiones-confirmadas.md)  
**Estado:** Propuesta para revisión

---

## Documentos

| Documento | Descripción |
|-----------|-------------|
| [Base de datos SGMI](./base-datos-sgmi.md) | **Diseño integral v2** — 30 tablas, vistas, reglas |
| [Arquitectura del sistema](./arquitectura-sistema.md) | Vista de componentes y flujos |
| [Modelo de datos](./modelo-datos.md) | ER Mermaid, entidades y reglas |
| [Schema inicial SQL](./schema-inicial.sql) | Script MySQL 8 ejecutable |
| [ADR-001 Stack y arquitectura](./adr/ADR-001-stack-arquitectura.md) | Laravel + Vue + MySQL |
| [ADR-001 Enmienda MySQL](./adr/ADR-001-enmienda-mysql.md) | Cambio PostgreSQL → MySQL |
| [ADR-002 ML Random Forest](./adr/ADR-002-servicio-ml-random-forest.md) | Servicio predictivo |
| [ADR-003 Integraciones](./adr/ADR-003-integraciones-siga-siaf.md) | APIs + simuladores |
| [ADR-004 Firma y sello](./adr/ADR-004-firma-digital-sello.md) | Documentos firmados |
| [Flujo documentario multietapa](./flujo-documentario-multietapa.md) | Derivación libre, devolución automática, trazabilidad |
| [Digitalización trámite documentario](../01_requisitos/digitalizacion-tramite-documentario.md) | Del cargo físico al seguimiento digital (PA-28) |
| [Catálogo normas documentales](../01_requisitos/catalogo-tipos-normas-documentales.md) | Tipos legales por área emisora (PA-29) |
| [Arquitectura interfaz y módulos](../01_requisitos/arquitectura-interfaz-y-modulos.md) | Una app, roles, menú dinámico |

## Prompts

| Prompt | Archivo |
|--------|---------|
| D-001 | [prompts/02_diseno/D-001_modelo_datos_v1.md](../../prompts/02_diseno/D-001_modelo_datos_v1.md) |
| D-002 | [prompts/02_diseno/D-002_decision_arquitectura_v1.md](../../prompts/02_diseno/D-002_decision_arquitectura_v1.md) |

## Trazabilidad diseño → requisitos

| Área diseño | RF / HU cubiertos |
|-------------|-------------------|
| `unidades`, `usuarios`, `traslados` | RF-NC-03, RF-NC-09, HU-SEC-05 |
| `tipos_documentales`, `numeraciones` | RF-DOC-01, RF-DOC-11, PA-09 |
| `documentos`, `firmas`, `sellos` | RF-DOC-12, RF-DOC-13, HU-DOC-08 |
| `expediente_movimientos` | RF-DOC-03–05, devolución automática (PA-27), derivación libre (PA-26) |
| `equipos`, `fichas_*` | RF-PAT-01–05, PA-15 |
| `ml_predicciones` | RF-PAT-08, HU-PAT-03 |
| `sync_logs`, `siaf_snapshots` | RI-SIGA-*, RI-SIAF-* |

## Próximo paso

Implementación Sprint 1: migraciones Laravel desde `schema-inicial.sql` y scaffold API auth.
