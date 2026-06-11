# Arquitectura del sistema — SGMI

**Versión:** 1.0  
**ADR relacionados:** ADR-001, ADR-002, ADR-003, ADR-004

---

## Vista de componentes

```mermaid
flowchart TB
    subgraph clientes [Red municipal]
        SPA[Vue 3 SPA]
    end

    subgraph backend [Servidor aplicación]
        API[Laravel 11 API REST]
        JOBS[Queue Workers / Scheduler]
        FS[Almacén archivos]
    end

    subgraph datos [Persistencia]
        DB[(MySQL 8)]
        REDIS[(Redis cache/sesión)]
    end

    subgraph ml [Servicio ML]
        PY[Python FastAPI + scikit-learn]
    end

    subgraph ext [Integraciones]
        SIGA_SIM[Simulador / API SIGA]
        SIAF_SIM[Simulador / API SIAF]
    end

    SPA -->|HTTPS JSON| API
    API --> DB
    API --> REDIS
    API --> FS
    API -->|HTTP interno| PY
    JOBS --> SIGA_SIM
    JOBS --> SIAF_SIM
    JOBS --> PY
    JOBS --> PG
```

---

## Capas de la aplicación (Laravel)

| Capa | Responsabilidad | Ejemplo |
|------|-----------------|---------|
| **Controllers** | HTTP, validación request, respuesta JSON | `ExpedienteController` |
| **Form Requests** | Reglas de validación | `StoreExpedienteRequest` |
| **Services** | Lógica de negocio | `ExpedienteService`, `FirmaDigitalService` |
| **Repositories** | Acceso a datos | `ExpedienteRepository` |
| **Models** | Eloquent + relaciones | `Expediente`, `Equipo` |
| **Policies** | Autorización RBAC | `ExpedientePolicy` |
| **Jobs** | Sync SIGA/SIAF, ML batch | `SyncSigaPatrimonioJob` |
| **Events/Listeners** | Auditoría transversal | `AuditarAccion` |

---

## Módulos y paquetes lógicos

```
app/
├── Modules/
│   ├── Core/          # Auth, RBAC, auditoría, organigrama
│   ├── Documentaria/  # Expedientes, firmas, sellos
│   ├── Patrimonio/    # Equipos, fichas, incidencias
│   ├── Dashboard/     # Agregaciones y reportes
│   └── Integracion/   # SIGA, SIAF, simuladores
```

> Estructura modular dentro de monolito Laravel (ADR-001); no microservicios en Fase 1 excepto ML.

---

## Flujos principales

### Tramitación documentaria

```mermaid
sequenceDiagram
    participant U as Usuario
    participant API as Laravel API
    participant DB as MySQL

    U->>API: Registrar expediente (tipo, año)
    API->>DB: Incrementar numeración tipo-año
    API->>DB: Crear expediente + movimiento registro
    U->>API: Adjuntar documento
    U->>API: Firmar documento + aplicar sello
    API->>DB: documento_firmas + documento_sellos
    U->>API: Derivar / devolver con observación
    API->>DB: expediente_movimientos + actualizar unidad_actual
```

### Predicción ML

```mermaid
sequenceDiagram
    participant JOB as Scheduler
    participant API as Laravel
    participant ML as Python ML
    participant DB as MySQL

    JOB->>API: Disparar análisis predictivo
    API->>DB: Leer fichas + incidencias + equipos
    API->>ML: POST /predict/batch
    ML-->>API: probabilidades por equipo
    API->>DB: ml_predicciones + nivel semáforo
```

### Sincronización SIGA

```mermaid
sequenceDiagram
    participant JOB as Job diario / manual
    participant INT as IntegracionService
    participant SIGA as API SIGA o Simulador
    participant DB as MySQL

    JOB->>INT: syncPatrimonio()
    INT->>SIGA: GET /patrimonio/informatica
    SIGA-->>INT: JSON bienes
    INT->>DB: Upsert equipos + sync_log
```

---

## Seguridad

| Aspecto | Implementación |
|---------|----------------|
| Autenticación | Laravel Sanctum (SPA cookie o token API) |
| Contraseñas | bcrypt/argon2, política 8+ especiales |
| Bloqueo | `bloqueado_hasta` + contador intentos |
| RBAC | Roles + Policies por módulo y unidad |
| Auditoría | Tabla append-only; sin UPDATE/DELETE app |
| Red | Restricción IP/middleware red municipal (RNF-13) |
| Archivos | Storage privado; URLs firmadas temporales |

---

## Despliegue objetivo (Fase 1)

| Componente | Entorno desarrollo (XAMPP) | Producción municipal |
|------------|---------------------------|----------------------|
| PHP | 8.2+ vía XAMPP | 8.2+ |
| BD | MySQL 8 / MariaDB (XAMPP) | MySQL 8.0+ |
| Frontend | Vite dev server / build estático | Nginx sirve `public/` |
| ML | Python 3.11 local | Servicio interno mismo servidor |
| Redis | Opcional dev; recomendado prod | Obligatorio |

Charset BD: `utf8mb4_unicode_ci` (ADR-001).

---

## Capacidad (PA-22)

- **200** usuarios registrados, **100** concurrentes
- Redis sesiones + índices en búsqueda expedientes
- Queue para sync y ML fuera del request crítico
- Test de carga objetivo: 100 sesiones en búsqueda < 2s (RNF-01)
