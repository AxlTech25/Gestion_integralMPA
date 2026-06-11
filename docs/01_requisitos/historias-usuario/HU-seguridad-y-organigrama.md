# Historias de usuario — Seguridad, organigrama y auditoría

**Módulo:** NÚCLEO  
**Procesos:** Transversal, C.01, S.06  
**Versión:** 1.1 — decisiones PA-01 a PA-06, PA-03 confirmadas

---

## HU-SEC-01 — Autenticación local segura

| Campo | Valor |
|-------|-------|
| **ID** | HU-SEC-01 |
| **RF** | RF-NC-01, RF-NC-02 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** servidor público  
**Quiero** ingresar con usuario y contraseña gestionados en SGMI  
**Para** acceder solo a las funciones autorizadas de mi cargo

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Autenticación local

  Scenario: Login exitoso
    Dado que soy un usuario activo con credenciales locales válidas
    Cuando ingreso usuario y contraseña correctos
    Entonces accedo al sistema y veo mi bandeja principal
    Y se registra en auditoría el evento "login_exitoso"

  Scenario: Política de contraseña
    Dado que creo o cambio contraseña
    Cuando la contraseña tiene menos de 8 caracteres o sin caracteres especiales
    Entonces el sistema rechaza y muestra requisitos
    Cuando cumple mínimo 8 caracteres con especiales
    Entonces acepta la contraseña

  Scenario: Bloqueo temporal por intentos
    Dado que he fallado 5 intentos de login
    Cuando intento autenticarme dentro de 5 minutos
    Entonces mi cuenta queda bloqueada temporalmente
    Y tras 5 minutos puedo intentar nuevamente o UTIS reactiva manualmente
```

**Decisiones:** PA-01, PA-02

---

## HU-SEC-02 — Permisos por rol y organigrama

| Campo | Valor |
|-------|-------|
| **ID** | HU-SEC-02 |
| **RF** | RF-NC-03, RF-NC-04, RF-NC-10 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador UTIS  
**Quiero** asignar roles según gerencia real y unidad operativa  
**Para** que cada servidor vea solo lo permitido por su cargo

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Control de acceso basado en roles

  Scenario: Sin flujo para comités
    Dado unidad tipo comité o mesa (ORG-004 a ORG-014)
    Cuando configuro organigrama
    Entonces no aparece como destino de derivación documentaria

  Scenario: Gerente ve su gerencia
    Dado que soy gerente de Servicios Públicos
    Cuando accedo al dashboard operativo
    Entonces veo métricas solo de unidades bajo mi gerencia real

  Scenario: Vista ejecutiva Alcaldía y Gerencia Municipal
    Dado rol VISTA_EJECUTIVA (Alcaldía o Gerencia Municipal)
    Cuando ingreso al sistema
    Entonces accedo a paneles generales y consultas
    Y no tengo bandeja operativa ni derivación documentaria completa

  Scenario: Derivación por gerencia real
    Dado expediente en derivación
    Cuando selecciono destino
    Entonces solo veo unidades operativas y gerencias del organigrama real
```

**Decisiones:** PA-04, PA-05, PA-06

---

## HU-SEC-03 — Auditoría de operaciones

| Campo | Valor |
|-------|-------|
| **ID** | HU-SEC-03 |
| **RF** | RF-NC-05 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** institución  
**Quiero** registrar todas las acciones CRUD de forma inalterable  
**Para** cumplir control interno y trazabilidad

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Auditoría del sistema

  Scenario: Registro de firma digital
    Dado que un usuario firma un documento
    Cuando se completa la firma
    Entonces existe log con módulo "documentaria", acción "firmar", id documento

  Scenario: Logs no editables
    Dado rol operador o gerente
    Cuando intento modificar auditoría
    Entonces la operación es rechazada
```

**Dependencias:** HU-SEC-01

---

## HU-SEC-04 — Consulta auditoría OCI

| Campo | Valor |
|-------|-------|
| **ID** | HU-SEC-04 |
| **RF** | RF-NC-06 |
| **Prioridad** | Media |
| **Estado** | Aprobado |

**Como** auditor OCI  
**Quiero** consultar y filtrar registros de auditoría  
**Para** control concurrente sin alterar datos

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Consulta OCI

  Scenario: Filtro de auditoría
    Dado rol AUDITOR_OCI
    Cuando filtro por módulo, usuario y fechas
    Entonces obtengo eventos en solo lectura
    Y puedo exportar a CSV
```

---

## HU-SEC-05 — Administración de usuarios y traslados

| Campo | Valor |
|-------|-------|
| **ID** | HU-SEC-05 |
| **RF** | RF-NC-07, RF-NC-09 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador UTIS  
**Quiero** gestionar usuarios con una unidad activa y registrar traslados  
**Para** alinear accesos al personal vigente

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Gestión de usuarios

  Scenario: Una unidad activa
    Dado que creo usuario
    Cuando asigno unidad organizacional
    Entonces solo puede tener una unidad activa simultánea

  Scenario: Traslado de unidad
    Dado servidor trasladado de Obras a UTIS
    Cuando registro traslado con fecha
    Entonces unidad activa cambia a UTIS
    Y historial conserva unidad anterior con fecha de traslado
    Y expedientes pendientes quedan visibles para supervisor de unidad origen

  Scenario: Usuario sin unidad
    Cuando creo usuario sin unidad
    Entonces el sistema rechaza el registro
```

**Decisiones:** PA-03

---

## HU-ORG-01 — Catálogo de unidades (gerencia real)

| Campo | Valor |
|-------|-------|
| **ID** | HU-ORG-01 |
| **RF** | RF-NC-08 |
| **Prioridad** | Alta |
| **Estado** | Aprobado |

**Como** administrador UTIS  
**Quiero** mantener unidades operativas y gerencias del organigrama real  
**Para** derivación, roles y reportes

### Criterios de aceptación (Gherkin)

```gherkin
Feature: Organigrama institucional

  Scenario: Jerarquía gerencia-unidad
    Dado organigrama MPA
    Cuando consulto una gerencia
    Entonces veo unidades dependientes en estructura jerárquica real

  Scenario: Comités excluidos de derivación
    Dado unidad tipo comité
    Entonces no es destino válido en tramitación documentaria

  Scenario: Unidad inactiva
    Dado unidad marcada inactiva
    Cuando operador intenta derivar
    Entonces operación rechazada
```

**Decisiones:** PA-04, PA-05
