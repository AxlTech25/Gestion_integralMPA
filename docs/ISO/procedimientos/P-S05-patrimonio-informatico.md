# P-S05 — Control patrimonial de equipos informáticos

| Campo | Valor |
|-------|-------|
| **Código** | P-S05 |
| **Proceso** | S.05 — Control patrimonial (subconjunto TI) |
| **Norma** | ISO 9001:2015 — 7.1, 8.5.3 |
| **Módulo SGMI** | MOD-PAT-TI |
| **Versión** | 1.0 |
| **Fecha** | 2026-06-11 |

---

## 1. Objetivo

Asegurar inventario oficial de equipos informáticos municipales con custodio documentado, dueño del dato en Patrimonio y vista operativa para UTIS.

---

## 2. Responsabilidades

| Rol | Responsabilidad |
|-----|-----------------|
| Unidad de Patrimonio (ORG-059) | Registro oficial; código patrimonial SIGA |
| UTIS (ORG-061) | Fichas técnicas, mantenimiento, ML |
| Custodio (jefe de área) | Uso responsable; reporte de incidencias |

---

## 3. Procedimiento

### 3.1 Registro de equipo

1. Patrimonio registra equipo con código patrimonial único, marca, modelo, unidad y custodio.
2. Solo equipos **municipales**; rechazar duplicados de código.

**SGMI:** `InventarioPage`, permiso `pat.equipo.registrar`.

### 3.2 Vista UTIS

1. UTIS consulta inventario (vista parcial sin campos patrimoniales reservados).
2. No modifica registro base sin rol Patrimonio.

**Permiso:** `pat.equipo.consultar`.

### 3.3 Fichas técnicas y mantenimiento

1. UTIS registra ficha técnica y mantenimientos preventivos/correctivos.
2. Datos alimentan modelo predictivo ML.

**Permiso:** `pat.ficha.gestionar`.

### 3.4 Sincronización SIGA (cuando INT-SIGA esté activo)

1. Job diario o manual importa bienes informáticos.
2. Upsert por código; log en `sync_logs`.
3. Patrimonio valida discrepancias.

---

## 4. Registros

| Registro | Ubicación |
|----------|-----------|
| Equipos | `equipos` |
| Fichas | `fichas_tecnicas`, `fichas_mantenimiento` |
| Predicciones ML | `ml_predicciones` |

---

## 5. Indicadores

OC-04, OC-05 en [objetivos-calidad-anuales.md](../objetivos-calidad-anuales.md).

---

## Referencias

- [HU-patrimonial-ti.md](../../01_requisitos/historias-usuario/HU-patrimonial-ti.md)
- [HU-INT-01](../../01_requisitos/historias-usuario/HU-integraciones.md)

---

## Control de cambios

| Versión | Fecha | Cambio |
|---------|-------|--------|
| 1.0 | 2026-06-11 | Emisión inicial |
