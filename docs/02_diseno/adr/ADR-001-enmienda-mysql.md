# ADR-001 Enmienda — Cambio PostgreSQL → MySQL

| Campo | Valor |
|-------|-------|
| **ADR padre** | ADR-001 |
| **Estado** | Aceptado |
| **Fecha** | 2026-06-10 |
| **Solicitante** | Equipo SGMI |

---

## Cambio

Reemplazar **PostgreSQL 15+** por **MySQL 8.0+** (MariaDB en XAMPP) como motor de base de datos oficial.

## Motivo

- XAMPP incluye MySQL/MariaDB sin instalación adicional.
- Alineación con entorno de desarrollo del equipo.
- MySQL 8 soporta JSON, ENUM, CHECK y FULLTEXT requeridos por SGMI.

## Archivos actualizados

- `docs/02_diseno/schema-inicial.sql` — script MySQL InnoDB
- `docs/02_diseno/modelo-datos.md`
- `docs/02_diseno/arquitectura-sistema.md`
- `docs/02_diseno/adr/ADR-001-stack-arquitectura.md`
- `docs/01_requisitos/ficha-proyecto.md`

## Impacto técnico

| PostgreSQL | MySQL |
|------------|-------|
| BIGSERIAL | BIGINT UNSIGNED AUTO_INCREMENT |
| JSONB | JSON |
| CREATE TYPE ENUM | ENUM en columna |
| pg_trgm GIN | FULLTEXT en `asunto` |
| Extensiones | No requeridas |

## Sin cambio

- Laravel 11, Vue 3, Sanctum, Redis, Python ML, integraciones SIGA/SIAF.
