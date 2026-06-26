# Matriz de correspondencia ISO 9001 ↔ SGMI

**Versión:** 1.0  
**Fecha:** 2026-06-11

Esta matriz vincula requisitos ISO 9001:2015 con procesos institucionales, módulos del SGMI y evidencias auditables.

---

## Capítulo 4 — Contexto de la organización

| Cláusula | Requisito | Proceso | SGMI | Evidencia |
|----------|-----------|---------|------|-----------|
| 4.1 | Contexto | Transversal | — | [matriz-partes-interesadas.md](./matriz-partes-interesadas.md) |
| 4.2 | Partes interesadas | Transversal | — | Matriz PI; mapa procesos |
| 4.3 | Alcance SGC | Transversal | — | [sgc-alcance.md](./sgc-alcance.md) |
| 4.4 | SGC y procesos | Transversal | NÚCLEO | Manual SGC; organigrama en BD |

---

## Capítulo 5 — Liderazgo

| Cláusula | Requisito | Proceso | SGMI | Evidencia |
|----------|-----------|---------|------|-----------|
| 5.1 | Liderazgo | E.01 | MOD-DASH estratégico | Actas; dashboard gerencia |
| 5.2 | Política calidad | E.01 | — | [politica-calidad-plantilla.md](./politica-calidad-plantilla.md) |
| 5.3 | Roles | Transversal | NÚCLEO RBAC | `roles`, `permisos`, `usuario_role` |
| 5.3 | Vista ejecutiva | E.01 | Rol VISTA_EJECUTIVA | Sin bandeja operativa |

---

## Capítulo 6 — Planificación

| Cláusula | Requisito | Proceso | SGMI | Evidencia |
|----------|-----------|---------|------|-----------|
| 6.1 | Riesgos | E.01, S.06 | MOD-DASH alertas TI | Semáforo ML; NC registradas |
| 6.2 | Objetivos calidad | E.01 / E.02 | MOD-DASH KPIs | [objetivos-calidad-anuales.md](./objetivos-calidad-anuales.md) |
| 6.3 | Cambios | Transversal | Repo + migraciones | Git; ADRs en `docs/02_diseno/adr/` |

---

## Capítulo 7 — Apoyo

| Cláusula | Requisito | Proceso | SGMI | Evidencia |
|----------|-----------|---------|------|-----------|
| 7.1 | Recursos | S.06 | MOD-PAT-TI | Inventario equipos |
| 7.2 | Competencias | Transversal | NÚCLEO roles | RolePermisoSeeder; capacitación |
| 7.3 | Comunicación | S.01 | MOD-DOC | Derivaciones; constancias |
| 7.5 | Info. documentada | S.01 | MOD-DOC | Expedientes, tipos, adjuntos |
| 7.5 | Trazabilidad | S.01 | MOD-DOC | `expediente_movimientos`, historial |
| 7.5 | Control registros | Transversal | NÚCLEO | `auditoria_logs`; export CSV |

---

## Capítulo 8 — Operación

| Cláusula | Requisito | Proceso | SGMI | Evidencia |
|----------|-----------|---------|------|-----------|
| 8.1 | Planificación operacional | S.01 | MOD-DOC | P-S01 |
| 8.5.1 | Identificación | S.01 | MOD-DOC | Código MEM-2026-0001 |
| 8.5.2 | Trazabilidad | S.01 | MOD-DOC | Trazabilidad expediente |
| 8.5.3 | Propiedad cliente | S.05 | MOD-PAT-TI | Custodio en equipo |
| 8.5.4 | Preservación | S.01 | MOD-DOC | Adjuntos; hash documentos |
| 8.5.5 | Actividades posteriores | S.06 | MOD-PAT-TI | Incidencias cerradas |
| 8.5.6 | Control cambios | S.01 | MOD-DOC | Movimientos auditados |
| 8.5.6 | Accesos | Transversal | NÚCLEO | P-NUCLEO |

---

## Capítulo 9 — Evaluación del desempeño

| Cláusula | Requisito | Proceso | SGMI | Evidencia |
|----------|-----------|---------|------|-----------|
| 9.1 | Seguimiento y medición | E.01 / E.02 | MOD-DASH | KPIs; gráficos gerencia/unidad |
| 9.1.2 | Satisfacción | S.06 | Incidencias | Tiempos de cierre UTIS |
| 9.2 | Auditoría interna | C.01 | NÚCLEO | F-ISO-03; `auditoria_logs` |
| 9.3 | Revisión dirección | E.01 | MOD-DASH | F-ISO-04 |

---

## Capítulo 10 — Mejora

| Cláusula | Requisito | Proceso | SGMI | Evidencia |
|----------|-----------|---------|------|-----------|
| 10.1 | General | Transversal | — | Objetivos + PDCA |
| 10.2 | NC y acciones correctivas | Transversal | MOD-CALIDAD | F-ISO-01/02; `no_conformidades` |
| 10.3 | Mejora continua | E.01 | MOD-DASH tendencias | `tendencia_pendientes` |

---

## Historias de usuario ↔ ISO

| HU | Módulo | Cláusulas ISO principales |
|----|--------|---------------------------|
| HU-SEC-01 a HU-SEC-05 | NÚCLEO | 7.1, 7.2, 7.5, 8.5.6, 9.2 |
| HU-ORG-01 | NÚCLEO | 4.4, 5.3 |
| HU-DOC-01 a HU-DOC-08 | MOD-DOC | 8.1, 8.5.1–8.5.6, 7.5 |
| HU-PAT-01 a HU-PAT-05 | MOD-PAT-TI | 7.1, 8.5.3, 8.5.5 |
| HU-DASH-01 a HU-DASH-03 | MOD-DASH | 6.2, 9.1, 9.3 |
| HU-INT-01 a HU-INT-04 | INT | 7.5, 9.1 (datos confiables) |

---

## Registros obligatorios en SGMI (Fase 1)

| Registro | Tabla / módulo | Retención sugerida |
|--------|----------------|-------------------|
| Expedientes y movimientos | `expedientes`, `expediente_movimientos` | Según norma archivo |
| Firmas y sellos | `documentos`, `firmas`, `sellos` | Idem expediente |
| Auditoría sistema | `auditoria_logs` | ≥ 5 años |
| Incidencias TI | `incidencias` | ≥ 3 años |
| Inventario equipos | `equipos`, fichas | Vida útil activo |
| Predicciones ML | `ml_predicciones` | 2 años |
| Sync SIAF | `siaf_ejecucion_snapshots` | 10 años (financiero) |
| No conformidades | `no_conformidades` (MOD-CALIDAD) | ≥ 5 años |
| Acciones correctivas | `acciones_correctivas` (MOD-CALIDAD) | ≥ 5 años |
