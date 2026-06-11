# ADR-001 — Stack tecnológico y arquitectura monolito modular

| Campo | Valor |
|-------|-------|
| **ADR** | ADR-001 |
| **Estado** | Aceptado (enmendado v1.1 — MySQL) |
| **Fecha** | 2026-06-10 |
| **Enmienda** | 2026-06-10 — PostgreSQL → MySQL 8+ |
| **Responsables** | Equipo SGMI |

---

## Contexto

SGMI Fase 1 requiere: tramitación documentaria con firma digital, inventario patrimonial, ML predictivo, integraciones SIGA/SIAF, 100 usuarios concurrentes en red municipal. El equipo desarrolla en **entorno XAMPP** con MySQL incluido. Requisitos v1.1 aprobados.

## Decisión

Adoptar **arquitectura monolito modular** con:

| Capa | Tecnología |
|------|------------|
| Backend API | PHP 8.2+, **Laravel 12** (Laravel 13 requiere PHP 8.3) |
| Frontend | **Vue 3** + Vite + Pinia |
| Base de datos | **MySQL 8.0+** (MariaDB 10.6+ en XAMPP) |
| Cache / sesión / queue | **Redis** (opcional en dev; recomendado prod) |
| Autenticación SPA | Laravel **Sanctum** |
| Archivos | Laravel Storage (local/NFS) |
| ML | Servicio **Python FastAPI** separado (ADR-002) |

Estructura de código: módulos lógicos bajo `app/Modules/` sin microservicios de negocio en Fase 1.

### Configuración Laravel (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sgmi_mpa
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

## Alternativas evaluadas

| Alternativa | Pros | Contras | Decisión |
|-------------|------|---------|----------|
| A) Laravel + Blade | Simple en XAMPP | No cumple RNF-02 (SPA reactiva) | Rechazada |
| B) Laravel API + Vue SPA | Reactividad, separación clara | Más setup inicial | **Seleccionada** |
| C) Microservicios | Escalabilidad | Complejidad excesiva para MVP | Rechazada |
| D) PostgreSQL 15+ | JSON avanzado, ENUM nativo | Requiere instalación adicional en XAMPP | Rechazada |
| E) **MySQL 8+ (XAMPP)** | Ya disponible, utf8mb4, JSON, InnoDB | FULLTEXT menos flexible que pg_trgm | **Seleccionada** |

## MySQL en XAMPP

- **Motor:** InnoDB, charset `utf8mb4`, collation `utf8mb4_unicode_ci`.
- **Tipos:** ENUM en columnas; JSON para metadata; `BIGINT UNSIGNED` para PKs.
- **Búsqueda asunto:** índice `FULLTEXT` en `expedientes.asunto`.
- **Schema referencia:** `docs/02_diseno/schema-inicial.sql`.
- **Fuente de verdad implementación:** migraciones Laravel (generadas desde diseño).

## Enmienda v1.1 (PostgreSQL → MySQL)

| Aspecto | Antes | Ahora |
|---------|-------|-------|
| BD principal | PostgreSQL 15+ | MySQL 8.0+ |
| Motivo | Diseño inicial | Alineación XAMPP y stack del equipo |
| Schema | tipos PostgreSQL | ENUM/JSON InnoDB |

## Consecuencias positivas

- Cero instalación extra de BD en desarrollo XAMPP.
- Laravel + MySQL es el stack más documentado para el equipo.
- Sanctum + Policies cubren RBAC por unidad y rol.
- Queues para SIGA, SIAF y ML sin bloquear UI.

## Consecuencias negativas

- Dos runtimes (PHP + Python) para ML.
- Redis adicional en producción (opcional en dev).
- Búsqueda full-text distinta a PostgreSQL (aceptable para MVP).

## Riesgos y mitigación

| Riesgo | Mitigación |
|--------|------------|
| 100 concurrentes | Índices, InnoDB, Redis sesiones en prod |
| Red municipal only | Middleware validación IP/subred |
| MariaDB vs MySQL | Probar en MariaDB de XAMPP; evitar features exclusivas |

## Criterios de verificación

- [ ] Laravel 11 + Vue 3 scaffold ejecuta en XAMPP/MySQL
- [ ] Sanctum login SPA funcional
- [ ] Estructura `app/Modules` creada
- [ ] `schema-inicial.sql` importa sin errores en MySQL 8
