# D-001 — Modelo de datos ER y SQL

| Campo | Valor |
|-------|-------|
| **Código** | D-001 |
| **Fase** | Diseño |
| **Versión** | v1 |
| **Modelo** | Cursor Agent — 2026-06-10 |
| **Estado** | Ejecutado |

---

## Prompt ejecutado

**Rol:** Arquitecto de base de datos.

**Contexto:** SGMI Fase 1. MySQL 8+. Módulos: núcleo RBAC, documentaria (numeración tipo+año, firma, sello, devolución), patrimonio (Patrimonio dueño, UTIS fichas), ML predicciones, integraciones SIGA/SIAF.

**Tarea:** Modelo ER normalizado + script SQL inicial.

**Formato:** Diagrama Mermaid erDiagram + SQL creación tablas.

**Restricciones:** 3FN razonable; PK/FK; timestamps; auditoría append-only; numeración por tipo_documental_id + anio.

---

## Resultado

| Artefacto | Ubicación |
|-----------|-----------|
| Modelo documentado | `docs/02_diseno/modelo-datos.md` |
| SQL MySQL | `docs/02_diseno/schema-inicial.sql` |
| Tablas | 24 |
| Dominios | Core, Documentaria, Patrimonio, Integración |

## Decisión

**Aprobado** — listo para migraciones Laravel.

## Lección aprendida

Separar `valor_patrimonial` y campos SIGA en modelo para vista parcial UTIS vía Policy/Resource, no duplicar tablas.
