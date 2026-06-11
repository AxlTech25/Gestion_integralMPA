# Historias de usuario — Gestión documentaria (S.01)

**Módulo:** MOD-DOC  
**Proceso:** S.01 — Gestión documentaria y comunicaciones  
**Unidad principal:** Secretaría General / Trámite Documentario (ORG-048)  
**Versión:** 1.1

---

## HU-DOC-01 — Registro de expediente con numeración por tipo

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-01 |
| **RF** | RF-DOC-01, RF-DOC-02 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** operador de Secretaría General  
**Quiero** registrar expedientes con numeración según tipo documental y año  
**Para** seguir la codificación institucional sin código global único

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Registro de expedientes

  Scenario: Numeración por tipo y año
    Dado tipo documental "Memorándum" y año 2026
    Cuando registro expediente con asunto, unidad origen y prioridad
    Entonces el sistema asigna código según reglas del tipo y año
    Y Secretaría General puede configurar prefijos por tipo documental
    Y el expediente queda en estado "Registrado"

  Scenario: Mismo año distinto tipo
    Dado dos tipos documentales diferentes en 2026
    Cuando registro expedientes de cada tipo
    Entonces cada uno sigue su propia secuencia por tipo-año

  Scenario: Campos obligatorios
    Dado que omito asunto, tipo o unidad origen
    Cuando intento guardar
    Entonces veo validación y no se crea el expediente
```

**Decisiones:** PA-07, PA-09

---

## HU-DOC-02 — Derivación y devolución

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-02 |
| **RF** | RF-DOC-03, RF-DOC-04, RF-DOC-05 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** funcionario con expediente en bandeja  
**Quiero** derivar o devolver expedientes con observaciones  
**Para** continuar el flujo sin traslado físico de papel

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Derivación y devolución

  Scenario: Derivación a gerencia real
    Dado expediente en mi bandeja
    Cuando derivo a unidad de gerencia real del organigrama
    Y ingreso proveído u observación
    Entonces expediente aparece en bandeja del destino
    Y se registra movimiento en historial

  Scenario: Devolución con observaciones
    Dado expediente recibido de unidad anterior
    Cuando devuelvo con observaciones obligatorias
    Entonces expediente regresa a bandeja de unidad anterior
    Y historial muestra acción "devolución" con observación

  Scenario: Devolución sin observación
    Cuando intento devolver sin observaciones
    Entonces el sistema rechaza la operación

  Scenario: Derivación a unidad inactiva
    Cuando destino es unidad inactiva
    Entonces operación rechazada
```

**Decisiones:** PA-05, PA-10

---

## HU-DOC-03 — Trazabilidad e historial

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-03 |
| **RF** | RF-DOC-06 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** supervisor o auditor  
**Quiero** ver línea de tiempo del expediente  
**Para** conocer estado, responsables, tiempos y observaciones

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Historial de expediente

  Scenario: Línea de tiempo
    Dado expediente con derivaciones y devoluciones
    Cuando abro trazabilidad
    Entonces veo cronológico: fecha, usuario, unidad, acción, observación
    Y tiempo de permanencia en cada oficina
    Y sin indicadores SLA (no hay plazos máximos institucionales)
```

**Decisiones:** PA-11

---

## HU-DOC-04 — Bandeja de pendientes

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-04 |
| **RF** | RF-DOC-07, RF-DOC-08 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** servidor público operativo  
**Quiero** bandeja personal con documentos pendientes  
**Para** priorizar firmas, proveídos y derivaciones

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Bandeja de pendientes

  Scenario: Bandeja personalizada
    Dado expedientes asignados a mi usuario o unidad
    Cuando abro bandeja
    Entonces veo código, asunto, tipo, prioridad, antigüedad y origen

  Scenario: Filtros reactivos
    Cuando filtro por prioridad y antigüedad
    Entonces lista se actualiza sin recargar página completa
```

---

## HU-DOC-05 — Adjuntos digitalizados

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-05 |
| **RF** | RF-DOC-09 |
| **Prioridad** | Media |
| **Estado** | Aprobado |

**Como** operador de trámite documentario  
**Quiero** adjuntar archivos al expediente  
**Para** conservar documento fuente digitalizado

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Adjuntos

  Scenario: Subir PDF
    Cuando subo PDF válido
    Entonces queda vinculado al expediente con registro en historial

  Scenario: Archivo no permitido
    Cuando subo ejecutable
    Entonces carga rechazada
```

---

## HU-DOC-06 — Búsqueda de expedientes

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-06 |
| **RF** | RF-DOC-10 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** funcionario autorizado  
**Quiero** buscar por código, asunto o unidad  
**Para** localizar trámites rápidamente

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Búsqueda

  Scenario: Por código tipo-año
    Cuando busco código según tipo documental y año
    Entonces resultados en menos de 2 segundos según permisos

  Scenario: Por asunto parcial
    Cuando busco texto parcial
    Entonces lista filtrada según rol
```

---

## HU-DOC-07 — Catálogo tipos documentales

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-07 |
| **RF** | RF-DOC-11 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador de Secretaría General  
**Quiero** cargar y mantener el catálogo institucional de tipos documentales  
**Para** registrar expedientes con clasificación oficial

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Tipos documentales

  Scenario: Cargar listado institucional
    Dado listado oficial de tipos de gestión municipal
    Cuando Secretaría General importa o registra tipos
    Entonces tipos disponibles al registrar expediente
    Y cada tipo puede tener prefijo/formato de numeración por año

  Scenario: Tipo inactivo
    Dado tipo marcado inactivo
    Cuando operador registra expediente
    Entonces tipo no aparece en selector
```

**Decisiones:** PA-07, PA-09

---

## HU-DOC-08 — Firma digital y sello

| Campo | Valor |
|-------|-------|
| **ID** | HU-DOC-08 |
| **RF** | RF-DOC-12, RF-DOC-13 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** funcionario autorizado  
**Quiero** firmar digitalmente cada documento y aplicar sello institucional  
**Para** validar documentos con trazabilidad legal

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Firma digital y sello

  Scenario: Firma obligatoria
    Dado documento en expediente listo para firma
    Cuando usuario autorizado firma
    Entonces documento queda firmado digitalmente
    Y se registra usuario, fecha y hora en auditoría
    Y documento no puede editarse sin nueva versión

  Scenario: Sello institucional
    Cuando documento es firmado o finalizado según flujo
    Entonces se aplica sello institucional visible en vista/PDF
    Y sello incluye identificación del documento

  Scenario: Sin firma incompleta
    Dado documento requiere firma
    Cuando intento derivar sin firmar
    Entonces sistema advierte o bloquea según configuración del tipo
```

**Decisiones:** PA-08
