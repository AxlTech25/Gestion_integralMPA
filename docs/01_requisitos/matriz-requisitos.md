# Matriz de requisitos funcionales y no funcionales

**Versión:** 1.3  
**Estado:** Aprobado con decisiones PA-01 … PA-29  
**Convención ID:** `RF-` requisito funcional, `RNF-` no funcional, `RI-` integración

---

## Requisitos funcionales — Núcleo (NÚCLEO)

| ID | Requisito | Proceso | Prioridad | Historia | Estado |
|----|-----------|---------|-----------|----------|--------|
| RF-NC-01 | Autenticación local con credenciales individuales gestionadas en SGMI | Transversal | Must | HU-SEC-01 | Aprobado |
| RF-NC-02 | Contraseña min. 8 caracteres con especiales; bloqueo 5 intentos / 5 min | Transversal | Must | HU-SEC-01 | Aprobado |
| RF-NC-03 | RBAC por organigrama (gerencias y unidades operativas; sin comités) | Transversal | Must | HU-SEC-02 | Aprobado |
| RF-NC-04 | Operador sin acceso a funciones de gerente sin permiso explícito | Transversal | Must | HU-SEC-02 | Aprobado |
| RF-NC-05 | Auditoría inalterable: usuario, fecha, hora, módulo, acción, entidad | C.01 | Must | HU-SEC-03 | Aprobado |
| RF-NC-06 | OCI consulta logs sin modificación | C.01 | Must | HU-SEC-04 | Aprobado |
| RF-NC-07 | UTIS gestiona usuarios, roles y unidades | S.06 | Must | HU-SEC-05 | Aprobado |
| RF-NC-08 | Catálogo de unidades según organigrama (gerencias reales) | Transversal | Must | HU-ORG-01 | Aprobado |
| RF-NC-09 | Un usuario pertenece a una sola unidad activa; traslados con historial | S.02 | Must | HU-SEC-05 | Aprobado |
| RF-NC-10 | Alcaldía y Gerencia Municipal: vista ejecutiva (paneles y consultas) | E.01 | Must | HU-SEC-02 | Aprobado |

---

## Requisitos funcionales — Gestión documentaria (MOD-DOC / S.01)

| ID | Requisito | Proceso | Prioridad | Historia | Estado |
|----|-----------|---------|-----------|----------|--------|
| RF-DOC-01 | Registrar expediente con numeración por tipo documental y año (Secretaría General) | S.01 | Must | HU-DOC-01 | Aprobado |
| RF-DOC-02 | Asociar unidad origen, asunto, tipo documental y prioridad | S.01 | Must | HU-DOC-01 | Aprobado |
| RF-DOC-03 | Derivar a unidad del organigrama; destino **libre** elegido por operador (PA-26) | S.01 | Must | HU-DOC-02 | Aprobado |
| RF-DOC-04 | Registrar observaciones y proveído en cada movimiento | S.01 | Must | HU-DOC-02 | Aprobado |
| RF-DOC-05 | Devolver con observación obligatoria; destino **automático** al remitente inmediato (PA-27) | S.01 | Must | HU-DOC-02 | Aprobado |
| RF-DOC-06 | Línea de tiempo: estado, responsables, tiempos por oficina; sustituye seguimiento por cargo físico (PA-28) | S.01 | Must | HU-DOC-03 | Aprobado |
| RF-DOC-07 | Bandeja personalizada de pendientes | S.01 | Must | HU-DOC-04 | Aprobado |
| RF-DOC-08 | Filtrar bandeja por prioridad, antigüedad y tipo | S.01 | Should | HU-DOC-04 | Aprobado |
| RF-DOC-09 | Adjuntar archivos digitalizados | S.01 | Should | HU-DOC-05 | Aprobado |
| RF-DOC-10 | Buscar por código, asunto o unidad en tiempo real | S.01 | Must | HU-DOC-06 | Aprobado |
| RF-DOC-11 | Catálogo de tipos documentales institucional; unidad emisora por tipo; gestión por área (PA-29) | S.01 | Must | HU-DOC-07 | Aprobado |
| RF-DOC-12 | Firma digital obligatoria en todos los documentos | S.01 | Must | HU-DOC-08 | Aprobado |
| RF-DOC-13 | Sello institucional en cada documento | S.01 | Must | HU-DOC-08 | Aprobado |

---

## Requisitos funcionales — Patrimonial TI (MOD-PAT-TI / S.05, S.06)

| ID | Requisito | Proceso | Prioridad | Historia | Estado |
|----|-----------|---------|-----------|----------|--------|
| RF-PAT-01 | Patrimonio registra equipos municipales (marca, modelo, serie, estado) | S.05 | Must | HU-PAT-01 | Aprobado |
| RF-PAT-02 | Asociar código patrimonial SIGA cuando exista | S.05 | Must | HU-PAT-01 | Aprobado |
| RF-PAT-03 | UTIS consulta vista parcial (datos relevantes para soporte) | S.06 | Must | HU-PAT-01 | Aprobado |
| RF-PAT-04 | Responsable documentado: jefe de área/unidad custodio | S.05 | Must | HU-PAT-01 | Aprobado |
| RF-PAT-05 | Fichas técnicas y de mantenimiento por equipo | S.06 | Must | HU-PAT-05 | Aprobado |
| RF-PAT-06 | Incidencias técnicas vinculadas a equipo | S.06 | Must | HU-PAT-02 | Aprobado |
| RF-PAT-07 | Ciclo incidencia: abierta → en atención → cerrada | S.06 | Must | HU-PAT-02 | Aprobado |
| RF-PAT-08 | ML Random Forest: probabilidad de falla (fichas + historial) | S.05 | Must | HU-PAT-03 | Aprobado |
| RF-PAT-09 | Semáforo de riesgo verde/amarillo/rojo | S.05 | Must | HU-PAT-04 | Aprobado |
| RF-PAT-10 | Lista equipos críticos para mantenimiento prioritario | S.05 | Must | HU-PAT-04 | Aprobado |

---

## Requisitos funcionales — Dashboard (MOD-DASH / E.01, E.02)

| ID | Requisito | Proceso | Prioridad | Historia | Estado |
|----|-----------|---------|-----------|----------|--------|
| RF-DASH-01 | Tiempos de respuesta y cuellos de botella en tramitación (sin SLA) | E.01 | Must | HU-DASH-01 | Aprobado |
| RF-DASH-02 | Filtrar por gerencia y unidad | E.01 | Must | HU-DASH-01 | Aprobado |
| RF-DASH-03 | Consolidar alertas críticas TI | E.01 | Must | HU-DASH-02 | Aprobado |
| RF-DASH-04 | Ejecución presupuestal SIAF (detalle limitado) | E.02 | Must | HU-DASH-03 | Aprobado |
| RF-DASH-05 | Gerente: su gerencia; Alta dirección: paneles ejecutivos | E.01 | Must | HU-DASH-01 | Aprobado |
| RF-DASH-06 | SIAF solo Presupuesto, Tesorería y Contabilidad | E.02 | Must | HU-DASH-03 | Aprobado |

---

## Requisitos de integración

| ID | Requisito | Sistema | Prioridad | Historia | Estado |
|----|-----------|---------|-----------|----------|--------|
| RI-SIGA-01 | Importar patrimonio informático vía API SIGA | SIGA | Must | HU-INT-01 | Aprobado |
| RI-SIGA-02 | Actualizar sin duplicar códigos patrimoniales | SIGA | Must | HU-INT-01 | Aprobado |
| RI-SIGA-03 | Importar áreas y personal vía API | SIGA | Must | HU-INT-02 | Aprobado |
| RI-SIGA-04 | Sincronización diaria + manual bajo demanda | SIGA | Must | HU-INT-01 | Aprobado |
| RI-SIGA-05 | Simulador SIGA para desarrollo sin datos reales | SIGA | Must | HU-INT-04 | Aprobado |
| RI-SIAF-01 | Lectura diaria ejecución presupuestal | SIAF | Must | HU-INT-03 | Aprobado |
| RI-SIAF-02 | Solo lectura; sin modificar SIAF | SIAF | Must | HU-INT-03 | Aprobado |
| RI-SIAF-03 | Simulador SIAF para desarrollo | SIAF | Must | HU-INT-04 | Aprobado |

---

## Requisitos no funcionales

| ID | Requisito | Categoría | Prioridad | Criterio verificable |
|----|-----------|-----------|-----------|-------------------|
| RNF-01 | Búsqueda expedientes < 2 s con 10k registros | Rendimiento | Must | Test de carga |
| RNF-02 | Sin recarga completa en búsquedas y formularios | UX | Must | Demo SPA |
| RNF-03 | Tablas densas, filtros avanzados | UX | Must | Revisión UX |
| RNF-04 | Modo contraste suave / oscuro | UX | Should | Toggle UI |
| RNF-05 | Disponibilidad horario laboral ≥ 99% | Disponibilidad | Should | Monitoreo |
| RNF-06 | Ley 29733 datos de personal | Legal | Must | Checklist |
| RNF-07 | Logs auditoría no editables | Seguridad | Must | Test DB |
| RNF-08 | Hash seguro contraseñas (bcrypt/argon2) | Seguridad | Must | Code review |
| RNF-09 | Cobertura tests ≥ 80% módulos críticos | Calidad | Should | CI |
| RNF-10 | Trazabilidad requisito → historia → código | Mantenibilidad | Must | Repositorio |
| RNF-11 | Interfaz 100% castellano | UX | Must | Revisión UI |
| RNF-12 | 100 usuarios concurrentes sin degradación | Rendimiento | Must | Test carga |
| RNF-13 | Acceso solo red municipal | Seguridad | Must | Config red |

---

## Aclaraciones v1.2 (PA-23 … PA-27)

| Tema | Detalle | Documento |
|------|---------|-----------|
| Interfaz | Una app SGMI; menú por rol; no apps por área | [arquitectura-interfaz-y-modulos.md](./arquitectura-interfaz-y-modulos.md) |
| Flujo multietapa | Expediente circula entre unidades con historial | [flujo-documentario-multietapa.md](../02_diseno/flujo-documentario-multietapa.md) |
| Rechazo | No usado en entidad; sin implementar Fase 1 | PA-25 |
| Rutas | Sin plantillas fijas por tipo documental | PA-26 |

---

## Trazabilidad resumida (v1.2)

| Módulo | RF | Must | Should |
|--------|-----|------|--------|
| NÚCLEO | 10 | 10 | 0 |
| MOD-DOC | 13 | 11 | 2 |
| MOD-PAT-TI | 10 | 10 | 0 |
| MOD-DASH | 6 | 6 | 0 |
| Integración | 8 | 8 | 0 |
| RNF | 13 | 10 | 3 |
