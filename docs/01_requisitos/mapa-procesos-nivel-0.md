# Mapa de procesos — Nivel 0

**Entidad:** Municipalidad Provincial de Acobamba  
**Versión:** 1.0  
**Fuente:** Mapa de Procesos Institucional (Nivel 0)  
**Relación SGMI:** cada proceso se vincula al módulo del sistema que lo soportará (Fase 1 o futuras).

---

## Visión general

El mapa describe cómo las **partes interesadas** generan necesidades que la municipalidad transforma, mediante procesos estratégicos, misionales y de soporte, en **resultados e impacto** para la provincia.

```
Partes interesadas → [Estratégicos | Misionales | Soporte] → Resultados e impacto
                              ↓
                    C.01 Control Institucional (transversal)
```

---

## Partes interesadas (entradas)

| # | Parte interesada | Necesidad típica | Impacto en SGMI |
|---|------------------|------------------|-----------------|
| PI-01 | Ciudadanía | Servicios públicos, trámites | Indirecto (datos de atención en futuras fases M.02) |
| PI-02 | Organizaciones sociales | Programas sociales, coordinación | Futuro M.03 |
| PI-03 | Empresas y comercios | Licencias, tributos | Futuro M.02 |
| PI-04 | Instituciones públicas y privadas | Cooperación, convenios | Futuro E.02 |
| PI-05 | Comunidad en general | Desarrollo local, ambiente | Transversal |

---

## 1. Procesos estratégicos — Dirección y asesoramiento

### E.01 — Dirección institucional

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Gobierno y dirección de la entidad: Concejo Municipal, Alcaldía, Gerencia Municipal, comités y mancomunidad. |
| **Unidades organigrama** | Concejo Municipal, Alcaldía, Gerencia Municipal, comités (Seguridad Ciudadana, Defensa Civil, Ambiental, etc.) |
| **SGMI Fase 1** | **MOD-DASH** — monitoreo de eficiencia y alertas para alta dirección |
| **SGMI futuro** | Reportes por comité, actas de sesiones |

### E.02 — Planeamiento, presupuesto y cooperación

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Racionalización, presupuesto, programación multianual de inversiones (PMI), cooperación técnica. |
| **Unidades organigrama** | Gerencia de Planeamiento y Presupuesto; Unidades de Planeamiento, Presupuesto, PMI, Cooperación Técnica |
| **SGMI Fase 1** | **MOD-DASH** + **INT-SIAF** — lectura de PIM y ejecución presupuestal |
| **SGMI futuro** | Cuadros de necesidades, seguimiento PMI |

### E.03 — Asesoramiento legal y defensa

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Asesoría jurídica y representación legal de la municipalidad. |
| **Unidades organigrama** | Gerencia de Asesoría Legal, Procuraduría Pública Municipal |
| **SGMI Fase 1** | **MOD-DOC** — derivación de expedientes a asesoría legal |
| **SGMI futuro** | Repositorio de dictámenes y plazos legales |

---

## 2. Procesos misionales — Cadena de valor principal

### M.01 — Gestión del desarrollo urbano, obras e infraestructura

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Ordenamiento territorial y catastro; GRD y defensa civil; ciclo de inversión pública (estudios, obras, supervisión, liquidación); gestión vial provincial (Instituto Vial Provincial). |
| **Unidades organigrama** | Gerencia de Desarrollo Urbano e Infraestructura; Sub Gerencia de Acondicionamiento Territorial; Unidades de Estudios, Obras, Supervisión y Liquidación; ATMS; Área Defensa Civil y GRD |
| **SGMI Fase 1** | **MOD-DOC** — expedientes de obras derivados internamente |
| **SGMI futuro** | Módulo de inversiones y seguimiento de obras |

### M.02 — Gestión de servicios públicos y tributación

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Seguridad ciudadana y transportes; limpieza pública, parques y jardines; comercialización e inocuidad alimentaria; matadero municipal; administración tributaria, fiscalización y ejecución coactiva; servicios al ciudadano y registro civil. |
| **Unidades organigrama** | Gerencia de Servicios Públicos y Administración Tributaria; unidades de Seguridad Ciudadana, Transportes, Limpieza, Comercialización, Matadero, Administración Tributaria, Fiscalización, Ejecución Coactiva, Servicios al Ciudadano |
| **SGMI Fase 1** | No incluido (referencia organigrama para roles) |
| **SGMI futuro** | Módulos tributarios y ventanilla interna |

### M.03 — Gestión del desarrollo social, económico y ambiental

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Programas sociales (DEMUNA, OMAPED, CIAM, ULE); desarrollo económico; gestión ambiental y saneamiento municipal. |
| **Unidades organigrama** | Gerencia de Desarrollo Social, Económico y Gestión Ambiental; sub gerencias y unidades sociales, ambientales y saneamiento |
| **SGMI Fase 1** | No incluido |
| **SGMI futuro** | Módulo de programas sociales y ambientales |

---

## 3. Procesos de soporte — Apoyo operativo

> **Nota institucional:** S.02 a S.06 pertenecen administrativamente a Planeamiento y Presupuesto, pero operan como procesos transversales de soporte.

### S.01 — Gestión documentaria y comunicaciones

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Trámite documentario, archivo central, imagen institucional. |
| **Unidades organigrama** | Secretaría General; Unidad de Trámite Documentario y Archivo; Unidad de Imagen Institucional |
| **SGMI Fase 1** | **MOD-DOC** — núcleo funcional principal |
| **Servicios** | Registro expedientes, derivación, trazabilidad, bandeja de pendientes |

### S.02 — Gestión de recursos humanos

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Talento humano: legajos, asistencia, beneficios (vía SIGA en la práctica institucional). |
| **Unidades organigrama** | Sub Gerencia de Gestión del Talento Humano |
| **SGMI Fase 1** | **INT-SIGA** — mapeo de personal para organigrama y usuarios |
| **SGMI futuro** | Solicitudes internas de personal (no reemplaza SIGA)

### S.03 — Gestión de abastecimiento

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Compras y contrataciones. |
| **Unidades organigrama** | Unidad de Abastecimiento |
| **SGMI Fase 1** | No incluido |
| **SGMI futuro** | Requerimientos vinculados a expedientes |

### S.04 — Gestión financiera

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Contabilidad y tesorería. |
| **Unidades organigrama** | Unidad de Contabilidad, Unidad de Tesorería |
| **SGMI Fase 1** | **INT-SIAF** — solo lectura para dashboard |
| **SGMI futuro** | No sustituye operación contable

### S.05 — Control patrimonial, maquinarias y equipos

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Registro de bienes patrimoniales, maquinaria pesada y equipos. |
| **Unidades organigrama** | Unidad de Patrimonio; Unidad de Maquinarias y Equipos |
| **SGMI Fase 1** | **MOD-PAT-TI** — subconjunto equipos informáticos + **INT-SIGA** |
| **SGMI futuro** | Patrimonio completo y maquinarias |

### S.06 — Tecnologías de la información y sistemas

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Soporte TI, redes, desarrollo de software. |
| **Unidades organigrama** | Unidad de Tecnología de la Información y Sistemas (UTIS) |
| **SGMI Fase 1** | **MOD-PAT-TI** — incidencias y alertas; **NÚCLEO** — administración |
| **SGMI futuro** | Gestión de proyectos de software y CMDB ampliado |

---

## 4. Procesos de evaluación y control

### C.01 — Control institucional

| Atributo | Detalle |
|----------|---------|
| **Descripción** | Auditorías y control concurrente. |
| **Unidades organigrama** | Oficina de Control Institucional (OCI) |
| **SGMI Fase 1** | **NÚCLEO** — auditoría transversal (consulta) |
| **SGMI futuro** | Reportes OCI personalizados |

---

## Resultados e impacto (salidas)

| Resultado | Procesos que contribuyen | Indicador SGMI (Fase 1) |
|-----------|--------------------------|-------------------------|
| Servicios públicos de calidad | M.02, S.01 | Tiempo de tramitación documentaria |
| Desarrollo local sostenible | E.02, M.01, M.03 | Ejecución presupuestal (SIAF) |
| Ambiente sano y sostenible | M.03 | — (futuro) |
| Seguridad y convivencia ciudadana | M.02 | — (futuro) |
| Ciudadanos satisfechos | Cadena completa | Eficiencia operativa en dashboard |

---

## Matriz proceso ↔ módulo SGMI

| Proceso | Fase 1 | Fase 2+ |
|---------|--------|---------|
| E.01 | MOD-DASH | Reportes dirección |
| E.02 | MOD-DASH, INT-SIAF | PMI integrado |
| E.03 | MOD-DOC | Dictámenes |
| M.01 | MOD-DOC | Obras e inversiones |
| M.02 | — | Tributación |
| M.03 | — | Social/ambiental |
| S.01 | MOD-DOC | Archivo digital |
| S.02 | INT-SIGA | RRHH interno |
| S.03 | — | Abastecimiento |
| S.04 | INT-SIAF | — |
| S.05 | MOD-PAT-TI | Patrimonio full |
| S.06 | MOD-PAT-TI, NÚCLEO | Proyectos TI |
| C.01 | NÚCLEO (auditoría) | OCI reports |
| Transversal | NÚCLEO | — |
