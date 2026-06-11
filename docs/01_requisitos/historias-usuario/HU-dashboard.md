# Historias de usuario — Dashboard estratégico (E.01 / E.02)

**Módulo:** MOD-DASH  
**Procesos:** E.01 Dirección institucional, E.02 Planeamiento y presupuesto  
**Versión:** 1.1

---

## HU-DASH-01 — Eficiencia en tramitación (sin SLA)

| Campo | Valor |
|-------|-------|
| **ID** | HU-DASH-01 |
| **RF** | RF-DASH-01, RF-DASH-02, RF-DASH-05 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** gerente  
**Quiero** ver tiempos de respuesta y cuellos de botella en tramitación  
**Para** mejorar eficiencia operativa de mi gerencia

**Como** Alcaldía o Gerencia Municipal  
**Quiero** paneles ejecutivos generales  
**Para** supervisión sin operar bandejas documentarias

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Dashboard tramitación

  Scenario: Gerente acotado a gerencia
    Dado gerente de Desarrollo Urbano
    Cuando abro dashboard tramitación
    Entonces veo solo unidades de mi gerencia real
    Y gráficos de tiempo promedio y pendientes por unidad
    Y sin alertas SLA (no hay plazos máximos institucionales)

  Scenario: Vista ejecutiva alta dirección
    Dado rol VISTA_EJECUTIVA (Alcaldía o Gerencia Municipal)
    Cuando abro paneles generales
    Entonces veo consolidado institucional y consultas
    Y no accedo a bandejas operativas

  Scenario: Actualización reactiva
    Cuando cambio filtro de periodo
    Entonces gráficos se actualizan sin recarga completa
```

**Decisiones:** PA-06, PA-11

---

## HU-DASH-02 — Consolidación alertas TI

| Campo | Valor |
|-------|-------|
| **ID** | HU-DASH-02 |
| **RF** | RF-DASH-03 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** alta dirección o gerente autorizado  
**Quiero** panel resumen de riesgos TI  
**Para** anticipar fallas de infraestructura

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Panel alertas TI

  Scenario: Resumen ejecutivo
    Dado equipos en semáforo rojo y amarillo (Random Forest)
    Cuando abro panel infraestructura
    Entonces veo conteo por nivel y top equipos críticos con unidad

  Scenario: Operador sin acceso
    Dado rol operador sin permiso dashboard
    Cuando intento acceder
    Entonces acceso denegado
```

---

## HU-DASH-03 — Ejecución presupuestal (SIAF restringido)

| Campo | Valor |
|-------|-------|
| **ID** | HU-DASH-03 |
| **RF** | RF-DASH-04, RF-DASH-06 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** usuario de Presupuesto, Tesorería o Contabilidad  
**Quiero** ver ejecución presupuestal con detalle limitado  
**Para** monitorear metas sin operar SIAF

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Dashboard presupuestal SIAF

  Scenario: Acceso restringido por área
    Dado usuario de Unidad de Presupuesto, Tesorería o Contabilidad
    Cuando accedo dashboard presupuestal
    Entonces veo PIM y % ejecución con detalle limitado
    Y fecha última sincronización

  Scenario: Sin acceso otras áreas
    Dado operador de otra unidad sin rol financiero
    Cuando intento dashboard SIAF
    Entonces acceso denegado

  Scenario: Solo lectura
    Cuando uso el dashboard
    Entonces no hay acciones que modifiquen SIAF

  Scenario: Datos simulados en desarrollo
    Dado ambiente sin SIAF real
    Cuando sistema usa simulador
    Entonces dashboard muestra datos de prueba identificados como simulación
```

**Decisiones:** PA-18, PA-19
