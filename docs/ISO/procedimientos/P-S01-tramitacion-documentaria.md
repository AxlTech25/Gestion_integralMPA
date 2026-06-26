# P-S01 — Tramitación documentaria interna

| Campo | Valor |
|-------|-------|
| **Código** | P-S01 |
| **Proceso** | S.01 — Gestión documentaria y comunicaciones |
| **Norma** | ISO 9001:2015 — 8.1, 8.5.1–8.5.6 |
| **Módulo SGMI** | MOD-DOC |
| **Versión** | 1.0 |
| **Fecha** | 2026-06-11 |

---

## 1. Objetivo

Establecer el flujo de registro, tramitación, derivación, recepción, devolución, firma/sello y archivo de expedientes internos mediante el SGMI, garantizando trazabilidad y sustitución del seguimiento en hoja de cargo física (PA-28).

---

## 2. Alcance

Aplica a todas las unidades que tramitan documentación interna según organigrama y permisos SGMI. No incluye portal ciudadano.

---

## 3. Responsabilidades

| Rol | Responsabilidad |
|-----|-----------------|
| Secretaría General / Trámite Documentario | Tipos documentales; supervisión normas |
| Operador / Supervisor | Registro, derivación, recepción en su unidad |
| Gerente | Monitoreo dashboard de su gerencia |
| UTIS | Soporte del sistema |

---

## 4. Entradas

- Documento o solicitud interna.
- Tipo documental vigente y unidad emisora autorizada.
- Archivo adjunto (PDF/imagen) cuando aplique.

---

## 5. Procedimiento

### 5.1 Registro

1. Usuario con permiso `doc.expediente.registrar` accede a **Registro de expediente**.
2. Selecciona tipo documental (filtrado por unidad emisora — PA-29).
3. Completa asunto, prioridad y adjunto opcional.
4. El sistema genera código `TIPO-AÑO-SECUENCIAL` y estado inicial.

**SGMI:** `RegistroExpedientePage`, API `POST /api/expedientes`.

### 5.2 Tramitación en unidad

1. Expediente en bandeja de la unidad (`unidad_actual_id`).
2. Operador revisa, firma/sella si `requiere_firma_antes_derivar`.
3. Deriva a unidad destino activa con proveído.

**SGMI:** `BandejaPendientesPage`, `TrazabilidadExpedientePage`.

### 5.3 Recepción

1. Unidad destino recepciona desde bandeja (estado `por_recepcionar`).
2. Se registra movimiento y constancia digital.

**Permiso:** `doc.expediente.recepcionar`.

### 5.4 Devolución

1. Si documentación incompleta, devolver a unidad origen con proveído.
2. No usar estado “rechazado” en Fase 1 (PA-25).

**Permiso:** `doc.expediente.devolver`.

### 5.5 Archivo

1. Unidad competente archiva expediente cerrado.
2. Estado `archivado`; no editable en bandeja operativa.

**Permiso:** `doc.expediente.archivar`.

### 5.6 Consulta y trazabilidad

- Cualquier usuario con `doc.expediente.consultar` puede ver historial, permanencia en oficina y movimientos.

---

## 6. Salidas

- Expediente tramitado con historial completo.
- Constancia digital por movimiento (firma + sello).
- Indicadores en MOD-DASH.

---

## 7. Registros

| Registro | Ubicación |
|----------|-----------|
| Expediente | SGMI — `expedientes` |
| Movimientos | SGMI — `expediente_movimientos` |
| Firma/sello | SGMI — `documentos`, `firmas` |
| Auditoría | SGMI — `auditoria_logs` |

---

## 8. Indicadores

Ver [objetivos-calidad-anuales.md](../objetivos-calidad-anuales.md) — OC-01, OC-02, OC-03.

---

## 9. Referencias

- [HU-gestion-documentaria.md](../../01_requisitos/historias-usuario/HU-gestion-documentaria.md)
- [flujo-documentario-multietapa.md](../../02_diseno/flujo-documentario-multietapa.md)
- [digitalizacion-tramite-documentario.md](../../01_requisitos/digitalizacion-tramite-documentario.md)

---

## Control de cambios

| Versión | Fecha | Cambio |
|---------|-------|--------|
| 1.0 | 2026-06-11 | Emisión inicial |
