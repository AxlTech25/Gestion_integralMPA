# P-S06 — Soporte técnico e incidencias

| Campo | Valor |
|-------|-------|
| **Código** | P-S06 |
| **Proceso** | S.06 — Tecnologías de la información |
| **Norma** | ISO 9001:2015 — 8.5.5, 10.2 |
| **Módulo SGMI** | MOD-PAT-TI |
| **Versión** | 1.0 |
| **Fecha** | 2026-06-11 |

---

## 1. Objetivo

Canalizar fallas, averías y requerimientos de soporte TI mediante incidencias formalizadas, con ciclo de atención UTIS y trazabilidad para mejora (ISO 10.2).

---

## 2. Responsabilidades

| Rol | Responsabilidad |
|-----|-----------------|
| Servidor público | Reportar incidencia sobre equipo de su unidad |
| UTIS | Atender, cerrar con solución; actualizar estado operativo |
| Gerencia / UTIS | Revisar semáforo de riesgo ML |

---

## 3. Procedimiento

### 3.1 Reporte

1. Usuario identifica equipo (búsqueda por código/marca).
2. Registra tipo (falla, avería, requerimiento) y descripción ≥ 10 caracteres.
3. Estado inicial: **Abierta**.

**Permiso:** `pat.incidencia.reportar` o gestión UTIS.

**SGMI:** `IncidenciasPage`, `POST /api/incidencias`.

### 3.2 Atención UTIS

1. UTIS marca **En atención** (asignación automática).
2. Diagnóstico y trabajo técnico.

**Permiso:** `pat.incidencia.gestionar`.

### 3.3 Cierre

1. UTIS registra **solución** obligatoria.
2. Opcional: actualizar `estado_operativo` del equipo.
3. Estado **Cerrada** con fecha.

### 3.4 Seguimiento solicitante

1. Operador ve historial de sus reportes en **Soporte TI**.
2. No puede cerrar incidencias de otros.

---

## 4. Relación con no conformidades (ISO 10.2)

Incidencias TI recurrentes o con impacto institucional pueden escalarse a **F-ISO-01** (no conformidad) y **F-ISO-02** (acción correctiva).

---

## 5. Mantenimiento preventivo

1. UTIS registra mantenimiento en ficha del equipo.
2. Job `sgmi:ml-predict` (diario) recalcula riesgo.
3. Equipos en **rojo** priorizados en semáforo.

---

## 6. Registros

| Registro | Ubicación |
|----------|-----------|
| Incidencias | `incidencias` |
| Auditoría | `auditoria_logs` (acciones MOD-PAT-TI) |

---

## 7. Indicadores

OC-05, OC-06 en [objetivos-calidad-anuales.md](../objetivos-calidad-anuales.md).

---

## Control de cambios

| Versión | Fecha | Cambio |
|---------|-------|--------|
| 1.0 | 2026-06-11 | Emisión inicial |
