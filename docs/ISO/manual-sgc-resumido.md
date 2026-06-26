# Manual del Sistema de Gestión de la Calidad (resumen)

**Entidad:** Municipalidad Provincial de Acobamba  
**Norma de referencia:** ISO 9001:2015  
**Versión:** 1.0  
**Fecha:** 2026-06-11

---

## 1. Introducción

Este manual describe el Sistema de Gestión de la Calidad (SGC) de la Municipalidad Provincial de Acobamba y su relación con el **SGMI**, herramienta digital que soporta la operación, trazabilidad y evidencia de los procesos incluidos en el alcance.

**Documento completo de alcance:** [sgc-alcance.md](./sgc-alcance.md)

---

## 2. Política de calidad

La política de calidad institucional se establece en [politica-calidad-plantilla.md](./politica-calidad-plantilla.md), aprobada por la alta dirección.

**Principios aplicados (ISO 9000):** enfoque al cliente (servidor público y ciudadanía indirecta), liderazgo, compromiso del personal, enfoque a procesos, mejora, decisiones basadas en evidencia, gestión de relaciones.

---

## 3. Contexto de la organización (Cap. 4)

| Elemento | Documento |
|----------|-----------|
| Partes interesadas | [matriz-partes-interesadas.md](./matriz-partes-interesadas.md) |
| Alcance del SGC | [sgc-alcance.md](./sgc-alcance.md) |
| Mapa de procesos | [../01_requisitos/mapa-procesos-nivel-0.md](../01_requisitos/mapa-procesos-nivel-0.md) |
| Organigrama | [../01_requisitos/organigrama-institucional.md](../01_requisitos/organigrama-institucional.md) |

---

## 4. Liderazgo (Cap. 5)

| Responsabilidad | Unidad / rol |
|-----------------|--------------|
| Política y objetivos de calidad | Gerencia Municipal / Alcaldía |
| Operación documentaria | Secretaría General, unidades operativas |
| Dueño del dato patrimonial informático | Unidad de Patrimonio (ORG-059) |
| Soporte TI y SGMI | UTIS (ORG-061) |
| Auditoría interna del SGC | OCI (ORG-015) con apoyo UTIS |
| Revisión por la dirección | Gerencia Municipal (acta F-ISO-04) |

---

## 5. Planificación (Cap. 6)

| Elemento | Documento / herramienta |
|----------|-------------------------|
| Objetivos de calidad | [objetivos-calidad-anuales.md](./objetivos-calidad-anuales.md) |
| Riesgos y oportunidades | Matriz de riesgos (OCI / planeamiento); indicadores MOD-DASH |
| Cambios al SGMI | Metodología Prompt-Centered SDLC; control de versiones en repositorio |

---

## 6. Apoyo (Cap. 7)

| Requisito | Implementación |
|-----------|----------------|
| Recursos | Infraestructura XAMPP/red municipal; UTIS |
| Competencias | Roles SGMI (OPERADOR, GERENTE, UTIS, etc.) |
| Información documentada | Procedimientos en `docs/ISO/procedimientos/`; registros en SGMI |
| Trazabilidad | Expedientes, movimientos, auditoría, incidencias |

**Procedimiento de accesos:** [P-NUCLEO-control-accesos.md](./procedimientos/P-NUCLEO-control-accesos.md)

---

## 7. Operación (Cap. 8)

| Proceso | Código procedimiento | Módulo SGMI |
|---------|----------------------|-------------|
| Gestión documentaria | P-S01 | MOD-DOC |
| Patrimonio informático | P-S05 | MOD-PAT-TI |
| Soporte técnico | P-S06 | MOD-PAT-TI |
| Control de accesos | P-NUCLEO | NÚCLEO |

---

## 8. Evaluación del desempeño (Cap. 9)

| Actividad | Frecuencia | Evidencia |
|-----------|------------|-----------|
| Seguimiento de indicadores | Mensual | MOD-DASH |
| Auditoría interna | Anual (programa OCI) | F-ISO-03, logs SGMI |
| Revisión por la dirección | Semestral / anual | F-ISO-04 |
| Satisfacción / quejas internas | Continuo | Incidencias TI; NC F-ISO-01 |

---

## 9. Mejora (Cap. 10)

| Mecanismo | Formato |
|-----------|---------|
| No conformidad | F-ISO-01 |
| Acción correctiva | F-ISO-02 |
| Mejora continua | Objetivos anuales + revisión dirección |
| Corrección en operación | Devolución de expedientes (MOD-DOC); cierre incidencias (MOD-PAT-TI) |

---

## 10. Matriz de correspondencia

Detalle capítulo ISO ↔ SGMI: [matriz-requisito-iso-sgmi.md](./matriz-requisito-iso-sgmi.md)

---

## Control de documento

| Versión | Fecha | Descripción |
|---------|-------|-------------|
| 1.0 | 2026-06-11 | Primera edición alineada a SGMI Fase 1 |
