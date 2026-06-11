# Historias de usuario — Control patrimonial TI (S.05 / S.06)

**Módulo:** MOD-PAT-TI  
**Procesos:** S.05 Control patrimonial, S.06 Tecnologías de la información  
**Unidades:** Patrimonio (ORG-059) dueño del dato; UTIS (ORG-061) fichas y soporte  
**Versión:** 1.1

---

## HU-PAT-01 — Inventario patrimonial (Patrimonio / UTIS)

| Campo | Valor |
|-------|-------|
| **ID** | HU-PAT-01 |
| **RF** | RF-PAT-01 a RF-PAT-04 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** responsable de Patrimonio  
**Quiero** registrar equipos municipales con custodio documentado  
**Para** centralizar inventario con dueño de dato claro

**Como** administrador UTIS  
**Quiero** consultar datos relevantes del inventario  
**Para** soporte sin acceso al registro patrimonial completo

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Inventario equipos municipales

  Scenario: Registro por Patrimonio
    Dado rol PATRIMONIO
    Cuando registro equipo municipal (PC, servidor, impresora, red)
    Con marca, modelo, serie, estado y código patrimonial SIGA
    Entonces equipo queda en inventario oficial

  Scenario: Responsable custodio
    Cuando asigno responsable
    Entonces se documenta jefe de área o unidad como custodio
    Y no otorga permisos especiales de consulta al custodio

  Scenario: Vista parcial UTIS
    Dado rol UTIS
    Cuando consulto inventario
    Entonces veo datos relevantes: código, tipo, marca, modelo, estado, unidad, custodio
    Y no veo campos patrimoniales completos reservados a Patrimonio

  Scenario: Solo equipos municipales
    Cuando intento registrar equipo no municipal
    Entonces operación rechazada

  Scenario: Código patrimonial duplicado
    Cuando código ya existe
    Entonces rechaza y muestra registro existente
```

**Decisiones:** PA-13, PA-14, PA-15

---

## HU-PAT-02 — Incidencias técnicas

| Campo | Valor |
|-------|-------|
| **ID** | HU-PAT-02 |
| **RF** | RF-PAT-06, RF-PAT-07 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** servidor público  
**Quiero** reportar fallas o requerimientos de soporte  
**Para** que UTIS atienda por canal formal

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Incidencias técnicas

  Scenario: Reporte de incidencia
    Dado equipo registrado en inventario
    Cuando creo incidencia tipo falla, avería o requerimiento
    Entonces queda en estado "Abierta" y UTIS la ve en panel soporte

  Scenario: Ciclo UTIS
    Cuando UTIS marca En atención y luego Cerrada con solución
    Entonces solicitante ve historial
    Y puede actualizarse estado operativo del equipo

  Scenario: Equipo no válido
    Cuando reporto sobre equipo inexistente sin permiso
    Entonces operación rechazada
```

---

## HU-PAT-03 — ML Random Forest predictivo

| Campo | Valor |
|-------|-------|
| **ID** | HU-PAT-03 |
| **RF** | RF-PAT-08 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador UTIS  
**Quiero** probabilidad de falla calculada con Random Forest  
**Para** planificar mantenimiento con datos de fichas e historial

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Análisis predictivo ML

  Scenario: Entrenamiento con fichas e historial
    Dado fichas técnicas, fichas de mantenimiento e historial del equipo
    Cuando job de análisis ejecuta Random Forest
    Entonces cada equipo tiene probabilidad de falla calculada
    Y resultado auditable: fecha, versión modelo, variables usadas

  Scenario: Equipo sin historial suficiente
    Dado equipo nuevo sin fichas ni incidencias
    Entonces probabilidad por defecto baja o "sin datos suficientes"
    Y no se clasifica como crítico sin evidencia

  Scenario: Reentrenamiento periódico
    Cuando se agregan nuevas fichas o incidencias
    Entonces modelo puede reentrenarse según programación configurada
```

**Decisiones:** PA-12

---

## HU-PAT-04 — Alertas preventivas (semáforo)

| Campo | Valor |
|-------|-------|
| **ID** | HU-PAT-04 |
| **RF** | RF-PAT-09, RF-PAT-10 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** UTIS o gerente con permiso  
**Quiero** tablero con semáforo de riesgo  
**Para** identificar mantenimiento o sustitución prioritaria

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Semáforo de riesgo

  Scenario: Indicadores ML
    Dado equipos con distinta probabilidad Random Forest
    Cuando abro tablero
    Entonces veo verde, amarillo y rojo según umbrales configurados

  Scenario: Lista priorizada
    Cuando filtro equipos en rojo
    Entonces lista con código patrimonial, unidad y custodio documentado
```

**Dependencias:** HU-PAT-03

---

## HU-PAT-05 — Fichas técnicas y de mantenimiento

| Campo | Valor |
|-------|-------|
| **ID** | HU-PAT-05 |
| **RF** | RF-PAT-05 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador UTIS  
**Quiero** registrar fichas técnicas y de mantenimiento por equipo  
**Para** alimentar soporte y el modelo predictivo

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Fichas de equipo

  Scenario: Ficha técnica
    Dado equipo en inventario Patrimonio
    Cuando UTIS registra ficha técnica
    Con CPU, RAM, almacenamiento, SO, red, antigüedad, estado componentes
    Entonces ficha vinculada al equipo y visible para UTIS
    Y Patrimonio mantiene dueño del registro base

  Scenario: Ficha de mantenimiento
    Cuando registro mantenimiento preventivo o correctivo
    Con fecha, tipo, descripción, técnico y resultado
    Entonces queda en historial del equipo
    Y alimenta entrenamiento Random Forest

  Scenario: Sin equipo patrimonial
    Cuando intento ficha sin equipo registrado por Patrimonio
    Entonces operación rechazada
```

**Decisiones:** PA-12, PA-15
