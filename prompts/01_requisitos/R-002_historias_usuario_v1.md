# R-002 — Historias de usuario y criterios de aceptación

| Campo | Valor |
|-------|-------|
| **Código** | R-002 |
| **Fase** | Requisitos |
| **Versión** | v1 |
| **Modelo** | Cursor Agent — 2026-06-10 |
| **Autor** | Equipo SGMI |
| **Estado** | Ejecutado |

---

## Prompt ejecutado

**Rol:** Actúa como Product Owner con experiencia en Scrum.

**Contexto:** Proyecto SGMI-MPA, Fase 1 MVP. Módulos: NÚCLEO, MOD-DOC (S.01), MOD-PAT-TI (S.05/S.06), MOD-DASH (E.01/E.02), INT-SIGA, INT-SIAF. Organigrama y mapa de procesos como base de roles y derivación.

**Tarea:** Convierte los requisitos aprobados en historias de usuario. Para cada historia: Como [rol], quiero [acción], para [beneficio]. Agrega criterios de aceptación en formato Gherkin.

**Formato:** ID HU-{MOD}-{NN}, prioridad, dependencias, RF vinculados, escenarios Gherkin.

**Restricciones:** Cada historia debe ser pequeña, testeable y estimable en un sprint. Solo alcance Fase 1.

---

## Resultado

| Módulo | Historias | Archivo |
|--------|-----------|---------|
| NÚCLEO | 6 | `docs/01_requisitos/historias-usuario/HU-seguridad-y-organigrama.md` |
| MOD-DOC | 6 | `docs/01_requisitos/historias-usuario/HU-gestion-documentaria.md` |
| MOD-PAT-TI | 4 | `docs/01_requisitos/historias-usuario/HU-patrimonial-ti.md` |
| MOD-DASH | 3 | `docs/01_requisitos/historias-usuario/HU-dashboard.md` |
| Integración | 3 | `docs/01_requisitos/historias-usuario/HU-integraciones.md` |
| **Total** | **22** | |

## Métricas

| Métrica | Valor |
|---------|-------|
| Historias generadas | 22 |
| Escenarios Gherkin | ~45 |
| RF cubiertos Fase 1 | 35/35 |

## Decisión

**Propuesta** — pendiente aprobación stakeholders antes de sprint 1.

## Lección aprendida

Vincular cada historia a código de proceso (S.01, E.01) y unidad ORG-xxx mejora trazabilidad con organigrama.
