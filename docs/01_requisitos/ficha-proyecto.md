# Ficha de proyecto — Sistema de Gestión Municipal Integral (SGMI)

| Campo | Contenido |
|-------|-----------|
| **Código** | SGMI-MPA |
| **Nombre** | Sistema de Gestión Municipal Integral (ERP Municipal) |
| **Cliente** | Municipalidad Provincial de Acobamba |
| **Versión documento** | 1.1 |
| **Fecha** | 2026-06-10 |
| **Decisiones** | [decisiones-confirmadas.md](./decisiones-confirmadas.md) |
| **Metodología** | Prompt-Centered SDLC v1.2 |

---

## Objetivo del sistema

Centralizar, automatizar y optimizar las operaciones internas de la Municipalidad Provincial de Acobamba mediante una plataforma modular que refleje el mapa de procesos institucional y el organigrama oficial, garantizando trazabilidad, seguridad y datos integrados.

## Usuarios principales

| Rol | Descripción |
|-----|-------------|
| Servidor público operativo | Registra expedientes, reporta incidencias, consulta bandejas |
| Supervisor de línea | Aprueba derivaciones, monitorea pendientes de su unidad |
| Gerente | Dashboard de su gerencia y operación documentaria de su área |
| Alcaldía / Gerencia Municipal | Vista ejecutiva: paneles generales y consultas (sin operación completa) |
| Unidad de Patrimonio | Registro de equipos municipales (dueño del dato) |
| Administrador de sistemas (UTIS) | Gestiona usuarios, roles, inventario TI y soporte |
| Auditor (OCI) | Consulta registros de auditoría (lectura) |

**Alcance de usuarios:** exclusivamente personal interno. No incluye portal ciudadano en esta fase.

## Problema que resuelve

- Tramitación documentaria con traslado físico de papel, **hojas de cargo** para seguimiento y baja trazabilidad (ver [digitalizacion-tramite-documentario.md](./digitalizacion-tramite-documentario.md)).
- Inventario de equipos informáticos disperso y mantenimiento reactivo.
- Falta de visión consolidada para la alta dirección sobre eficiencia y riesgos.
- Duplicidad de registros entre unidades y sistemas del Estado (SIGA, SIAF).

## Alcance incluido (Fase 1 — MVP)

| Módulo | Proceso | Código |
|--------|---------|--------|
| Seguridad y control de accesos | Transversal | NÚCLEO |
| Gestión documentaria y tramitación interna | S.01 | MOD-DOC |
| Control patrimonial — equipos informáticos | S.05 (subconjunto TI) | MOD-PAT-TI |
| Dashboard estratégico | E.01 / E.02 | MOD-DASH |
| Integración SIGA (lectura patrimonio y organigrama) | Soporte | INT-SIGA |
| Integración SIAF (lectura ejecución presupuestal) | Soporte | INT-SIAF |

## Fuera de alcance (Fase 1)

| Proceso | Razón |
|---------|-------|
| M.01, M.02, M.03 (operaciones misionales completas) | Fases posteriores; solo referencia en mapa de procesos |
| S.02 RRHH, S.03 Abastecimiento, S.04 Finanzas (operación) | No sustituye SIGA/SIAF; integración parcial en Fase 1 |
| S.05 maquinarias y equipos no informáticos | Fase 2 patrimonial ampliado |
| S.06 desarrollo de software (gestión de proyectos TI) | Fase posterior |
| C.01 control institucional (módulo propio OCI) | Solo auditoría transversal en núcleo |
| Portal ciudadano / ventanilla virtual | Fuera de alcance funcional |

## Stack tecnológico (tentativo — pendiente ADR)

| Capa | Tecnología propuesta |
|------|---------------------|
| Backend | PHP 8.2+ / Laravel 11 |
| Base de datos | MySQL 8.0+ (MariaDB XAMPP) |
| Frontend | Vue.js 3 + componentes reactivos |
| API | REST JSON |
| Autenticación | Local SGMI (sesión + contraseñas institucionales) |
| ML predictivo | Random Forest (fichas técnicas, mantenimiento, historial) |
| Integraciones | APIs SIGA; lectura SIAF; simuladores en desarrollo |
| Firma documental | Firma digital + sello en cada documento |

> **Confirmado (ADR-001 v1.1):** MySQL 8+ en XAMPP, Laravel 11, Vue 3.

## Restricciones de seguridad

- Autenticación **local** en SGMI (sin LDAP en Fase 1).
- Contraseña: mínimo 8 caracteres + caracteres especiales.
- Bloqueo: 5 intentos fallidos → bloqueo 5 minutos.
- Un usuario = una unidad activa; traslados con historial.
- RBAC por gerencias y unidades operativas (sin comités ni mesas).
- Auditoría inalterable de operaciones CRUD.
- Acceso solo en **red municipal**.
- No almacenar credenciales SIGA/SIAF en código.
- Ley N.° 29733 (datos personales — Perú).
- ~200 usuarios registrados; ~100 concurrentes.

## Convenciones de código

- PSR-12 (PHP), ESLint (Vue).
- Patrón Repository + Service Layer en Laravel.
- Migraciones versionadas; sin consultas SQL raw sin justificación.
- Nombres en español para dominio de negocio; inglés para código técnico.

## Criterios de aceptación globales (Definition of Done ampliada)

- [ ] Historia de usuario con criterios Gherkin aprobados.
- [ ] Prompt usado registrado y versionado en `/prompts`.
- [ ] Código revisado por humano responsable.
- [ ] Tests relevantes pasan en CI.
- [ ] Sin vulnerabilidades críticas ni secretos expuestos.
- [ ] Documentación técnica actualizada.
- [ ] ADR para decisiones arquitectónicas significativas.
- [ ] Trazabilidad: requisito → historia → módulo → proceso institucional.

## Documentos relacionados

- [Mapa de procesos Nivel 0](./mapa-procesos-nivel-0.md)
- [Organigrama institucional](./organigrama-institucional.md)
- [Alcance y priorización](./alcance-y-priorizacion.md)
- [Matriz de requisitos](./matriz-requisitos.md)
- [Preguntas de aclaración](./preguntas-aclaracion.md)
- [Decisiones confirmadas](./decisiones-confirmadas.md)
- [Digitalización del trámite documentario](./digitalizacion-tramite-documentario.md)
- [Catálogo tipos de normas y documentos legales](./catalogo-tipos-normas-documentales.md)
