# Historias de usuario — Integraciones SIGA y SIAF

**Módulos:** INT-SIGA, INT-SIAF  
**Versión:** 1.1

---

## HU-INT-01 — Importación patrimonio SIGA vía API

| Campo | Valor |
|-------|-------|
| **ID** | HU-INT-01 |
| **RF** | RI-SIGA-01, RI-SIGA-02, RI-SIGA-04 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador UTIS o Patrimonio  
**Quiero** importar bienes informáticos desde API SIGA  
**Para** evitar digitación manual y alinear códigos

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Importación SIGA Patrimonio API

  Scenario: Sincronización diaria
    Dado job nocturno configurado
    Cuando ejecuta contra API SIGA o simulador
    Entonces crea o actualiza inventario sin duplicar códigos patrimoniales
    Y reporta insertados, actualizados y errores

  Scenario: Sincronización manual
    Dado usuario UTIS o Patrimonio autorizado
    Cuando solicita sincronización manual
    Entonces ejecuta importación inmediata con mismo reporte

  Scenario: Solo equipos informáticos municipales
    Cuando API devuelve bienes mixtos
    Entonces solo procesa categoría informática municipal
```

**Decisiones:** PA-16, PA-17

---

## HU-INT-02 — Mapeo personal y oficinas SIGA

| Campo | Valor |
|-------|-------|
| **ID** | HU-INT-02 |
| **RF** | RI-SIGA-03 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador UTIS  
**Quiero** sincronizar áreas y personal vía API SIGA  
**Para** organigrama y usuarios con una unidad activa

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Sincronización organigrama SIGA

  Scenario: Importar unidades gerencia real
    Cuando sincronizo vía API
    Entonces unidades mapean a organigrama real sin comités
    Y jerarquía gerencia-unidad se mantiene

  Scenario: Personal y unidad activa
    Dado personal activo SIGA
    Cuando sincronizo
    Entonces puedo vincular usuario SGMI con una unidad activa
    Y traslados posteriores se gestionan en SGMI con historial

  Scenario: Personal cesado
    Dado personal inactivo en SIGA
    Cuando sincronizo
    Entonces sugiero desactivación de usuario (confirmación manual)
```

**Decisiones:** PA-03, PA-17

---

## HU-INT-03 — Lectura ejecución SIAF

| Campo | Valor |
|-------|-------|
| **ID** | HU-INT-03 |
| **RF** | RI-SIAF-01, RI-SIAF-02 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador de planeamiento  
**Quiero** importar ejecución presupuestal diaria desde SIAF  
**Para** alimentar dashboard financiero restringido

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Integración SIAF lectura

  Scenario: Job diario
    Cuando job nocturno ejecuta
    Entonces almacena snapshot PIM y ejecución con detalle limitado
    Y registra log de sincronización

  Scenario: Solo lectura
    Entonces conector sin operaciones de escritura a SIAF

  Scenario: Fallo de origen
    Cuando SIAF no responde
    Entonces dashboard muestra datos anteriores con alerta
```

**Decisiones:** PA-17, PA-18

---

## HU-INT-04 — Simuladores SIGA y SIAF

| Campo | Valor |
|-------|-------|
| **ID** | HU-INT-04 |
| **RF** | RI-SIGA-05, RI-SIAF-03 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** equipo de desarrollo  
**Quiero** simuladores de SIGA y SIAF  
**Para** desarrollar y probar sin registros reales

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Simuladores integración

  Scenario: Simulador SIGA
    Dado ambiente desarrollo sin SIGA real
    Cuando configuro integración en modo simulación
    Entonces API simulada devuelve patrimonio, áreas y personal de prueba
    Y UI indica datos simulados

  Scenario: Simulador SIAF
    Dado ambiente desarrollo sin SIAF real
    Cuando configuro modo simulación
    Entonces devuelve ejecución presupuestal de prueba
    Y dashboard presupuestal funciona end-to-end

  Scenario: Cambio a producción
    Cuando existan credenciales y APIs reales
    Entonces configuración permite cambiar de simulador a API real sin cambio de código de negocio
```

**Decisiones:** PA-19
