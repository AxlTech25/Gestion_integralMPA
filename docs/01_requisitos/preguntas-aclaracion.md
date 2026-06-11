# Preguntas de aclaración y supuestos

**Versión:** 1.1  
**Origen:** Análisis R-001 (detección de ambigüedades)  
**Estado:** **Respondido** — validado por stakeholders (2026-06-10)

---

## Resumen de decisiones confirmadas

| ID | Decisión |
|----|----------|
| **PA-01** | Autenticación **local** en SGMI (sin LDAP/AD en Fase 1). |
| **PA-02** | Credenciales locales: contraseña mín. **8 caracteres + especiales**; bloqueo **5 intentos / 5 minutos**. |
| **PA-03** | Un usuario = **una unidad** a la vez; puede **rotar/trasladarse** (historial de traslados). |
| **PA-04** | Comités y mesas **sin flujo** en SGMI; solo **unidades operativas y gerencias**. |
| **PA-05** | Derivación documentaria por **gerencia real** (unidad operativa). |
| **PA-06** | Alcaldía y Gerencia Municipal: solo **vista ejecutiva** (paneles generales y consultas). |
| **PA-07** | Catálogo de tipos documentales **existe**; se cargará el listado institucional. |
| **PA-08** | **Firma digital + sello** obligatorios en **todos** los documentos. |
| **PA-09** | Códigos gestionados por **Secretaría General**; **no globales únicos**; numeración por **tipo + año**. |
| **PA-10** | Con observaciones se puede **devolver** expediente a unidad anterior. |
| **PA-11** | **No** existen plazos máximos por trámite (sin SLA). |
| **PA-12** | **ML Random Forest** con fichas técnicas y de mantenimiento + historial del equipo. |
| **PA-13** | Solo equipos municipales; **Patrimonio** registra; **UTIS** ve datos relevantes (vista parcial). |
| **PA-14** | Responsable = **jefe de área/unidad** (documentación de custodia, no permisos de vista). |
| **PA-15** | **Patrimonio** dueño del dato; **UTIS** usa datos para fichas técnicas y de mantenimiento. |
| **PA-16** | Integración SIGA vía **APIs**. |
| **PA-17** | Sincronización **diaria** + **manual** bajo demanda del usuario. |
| **PA-18** | SIAF visible solo para **Presupuesto, Tesorería y Contabilidad**; detalle limitado. |
| **PA-19** | SIGA y SIAF **simulados** en desarrollo (sin registros reales aún). |
| **PA-20** | Uso **solo en red municipal**. |
| **PA-21** | Interfaz **100% en castellano**. |
| **PA-22** | ~**200** usuarios registrados; ~**100** concurrentes. |

---

## Detalle por hallazgo

### Autenticación e identidad

| # | Tipo | Hallazgo | Prioridad | **Respuesta confirmada** |
|---|------|----------|-----------|--------------------------|
| PA-01 | Implícito | Autenticación local vs federada | Alta | Autenticación local en SGMI. |
| PA-02 | Ambigüedad | Política de contraseñas | Media | Local SGMI; min. 8 caracteres + especiales; bloqueo 5 intentos por 5 minutos. |
| PA-03 | Implícito | Personal en múltiples unidades | Media | Una unidad activa; rotación/traslado permitido con historial; no simultaneidad. |

### Organigrama y roles

| # | Tipo | Hallazgo | Prioridad | **Respuesta confirmada** |
|---|------|----------|-----------|--------------------------|
| PA-04 | Implícito | Comités en flujo documentario | Baja | Sin flujo; solo unidades operativas y gerencias. |
| PA-05 | Contradicción | Derivación administrativa vs operativa | Alta | Gerencia real (unidad operativa). |
| PA-06 | Implícito | Permisos Alcaldía / Gerencia Municipal | Media | Vista ejecutiva: paneles generales y consultas; sin operación documentaria completa. |

### Gestión documentaria (S.01)

| # | Tipo | Hallazgo | Prioridad | **Respuesta confirmada** |
|---|------|----------|-----------|--------------------------|
| PA-07 | Implícito | Catálogo tipos documentales | Alta | Listado institucional existente; se agregará al sistema. |
| PA-08 | Implícito | Firma digital | Alta | Firma digital + sello en cada documento (MVP). |
| PA-09 | Ambigüedad | Formato de código | Media | Secretaría General administra; códigos por tipo documental y año (no único global). |
| PA-10 | Implícito | Devolución de expedientes | Media | Devolución a unidad anterior permitida con observaciones. |
| PA-11 | Implícito | SLA por trámite | Media | No hay plazos máximos; sin módulo SLA en Fase 1. |

### Patrimonial TI (S.05 / S.06)

| # | Tipo | Hallazgo | Prioridad | **Respuesta confirmada** |
|---|------|----------|-----------|--------------------------|
| PA-12 | Ambigüedad | Modelo predictivo | Alta | Random Forest; datos de fichas técnicas, mantenimiento e historial. |
| PA-13 | Implícito | Equipos no patrimoniales | Media | Solo equipos municipales; Patrimonio registra; UTIS vista parcial. |
| PA-14 | Implícito | Responsable del equipo | Media | Jefe de área/unidad como custodio documentado; no define permisos de consulta. |
| PA-15 | Contradicción | Dueño del dato | Alta | Patrimonio dueño; UTIS consume para fichas. |

### Integraciones SIGA / SIAF

| # | Tipo | Hallazgo | Prioridad | **Respuesta confirmada** |
|---|------|----------|-----------|--------------------------|
| PA-16 | Implícito | Formato SIGA | Alta | APIs de exportación SIGA. |
| PA-17 | Implícito | Frecuencia sync | Media | Diaria + manual cuando el usuario lo requiera. |
| PA-18 | Implícito | Detalle SIAF | Media | Solo Presupuesto, Tesorería, Contabilidad; registros con detalle limitado. |
| PA-19 | Implícito | Ambiente de prueba | Alta | Simulación SIGA/SIAF hasta tener registros reales. |

### UX y despliegue

| # | Tipo | Hallazgo | Prioridad | **Respuesta confirmada** |
|---|------|----------|-----------|--------------------------|
| PA-20 | Implícito | Acceso remoto | Media | Solo red municipal. |
| PA-21 | Implícito | Idioma | Baja | 100% castellano. |
| PA-22 | Implícito | Usuarios concurrentes | Media | 200 usuarios; 100 concurrentes. |

---

## Impacto en requisitos (v1.1)

| Área | Cambio principal |
|------|------------------|
| NÚCLEO | Política contraseñas; rol `VISTA_EJECUTIVA`; traslado de unidad |
| MOD-DOC | Firma digital + sello; códigos por tipo/año; devolución; catálogo tipos |
| MOD-PAT-TI | Fichas técnicas/mantenimiento; Random Forest; Patrimonio vs UTIS |
| MOD-DASH | SIAF restringido; sin métricas SLA |
| Integración | APIs SIGA; simuladores SIGA/SIAF |

---

## Próximos pasos

1. ~~Validar preguntas Alta~~ — Completado.
2. Diseño (D-01): modelo ER con firmas, sellos, fichas, numeración por tipo.
3. Diseño (D-02): ADR arquitectura + servicio ML (Random Forest) y simuladores integración.
