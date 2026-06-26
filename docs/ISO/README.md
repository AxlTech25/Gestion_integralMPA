# Sistema de Gestión de la Calidad (SGC) — ISO 9001:2015

**Entidad:** Municipalidad Provincial de Acobamba  
**Sistema de apoyo:** SGMI (Sistema de Gestión Municipal Integral)  
**Versión:** 1.0  
**Fecha:** 2026-06-11

---

## Referencias normativas

| Documento | Ubicación |
|-----------|-----------|
| ISO 9000:2015 — Fundamentos y vocabulario | [ISO 9000 -2015 - ESPAÑOL.pdf](./ISO%209000%20-2015%20-%20ESPA%C3%91OL.pdf) |
| ISO 9001:2015 — Requisitos | [ISO 9001 -2015 - ESPAÑOL.pdf](./ISO%209001%20-2015%20-%20ESPA%C3%91OL.pdf) |

> La certificación ISO 9001 es de la **organización** (municipalidad), no del software. El SGMI provee **evidencia, trazabilidad y control** de los procesos incluidos en el alcance del SGC.

---

## Estructura de documentación

```
docs/ISO/
├── README.md                          ← Este índice
├── manual-sgc-resumido.md             ← Manual del SGC (resumen ejecutivo)
├── sgc-alcance.md                     ← Alcance y exclusiones del SGC
├── politica-calidad-plantilla.md      ← Plantilla de política (firma alta dirección)
├── matriz-partes-interesadas.md       ← Cap. 4.2 ISO 9001
├── objetivos-calidad-anuales.md       ← Cap. 6.2 ISO 9001
├── matriz-requisito-iso-sgmi.md       ← ISO ↔ proceso ↔ SGMI ↔ evidencia
├── procedimientos/
│   ├── P-S01-tramitacion-documentaria.md
│   ├── P-S05-patrimonio-informatico.md
│   ├── P-S06-soporte-tecnico.md
│   └── P-NUCLEO-control-accesos.md
└── formatos/
    ├── F-ISO-01-no-conformidad.md
    ├── F-ISO-02-accion-correctiva.md
    ├── F-ISO-03-lista-verificacion-auditoria-interna.md
    └── F-ISO-04-acta-revision-direccion.md
```

---

## Documentos del SGC

| Documento | Capítulo ISO 9001 | Estado |
|-----------|-------------------|--------|
| [Política de calidad (plantilla)](./politica-calidad-plantilla.md) | 5.2 | Plantilla — pendiente firma |
| [Alcance del SGC](./sgc-alcance.md) | 4.3 | Aprobado v1.0 |
| [Manual SGC resumido](./manual-sgc-resumido.md) | 4.4 | Aprobado v1.0 |
| [Partes interesadas](./matriz-partes-interesadas.md) | 4.2 | Aprobado v1.0 |
| [Objetivos de calidad](./objetivos-calidad-anuales.md) | 6.2 | Plantilla 2026 |
| [Matriz ISO ↔ SGMI](./matriz-requisito-iso-sgmi.md) | Transversal | Aprobado v1.0 |

---

## Procedimientos operativos

| Código | Proceso | Procedimiento |
|--------|---------|---------------|
| P-S01 | S.01 Gestión documentaria | [P-S01-tramitacion-documentaria.md](./procedimientos/P-S01-tramitacion-documentaria.md) |
| P-S05 | S.05 Control patrimonial (TI) | [P-S05-patrimonio-informatico.md](./procedimientos/P-S05-patrimonio-informatico.md) |
| P-S06 | S.06 Tecnologías de la información | [P-S06-soporte-tecnico.md](./procedimientos/P-S06-soporte-tecnico.md) |
| P-NUCLEO | Transversal seguridad | [P-NUCLEO-control-accesos.md](./procedimientos/P-NUCLEO-control-accesos.md) |

---

## Formatos y registros

| Código | Uso | Formato |
|--------|-----|---------|
| F-ISO-01 | No conformidad | [F-ISO-01-no-conformidad.md](./formatos/F-ISO-01-no-conformidad.md) |
| F-ISO-02 | Acción correctiva | [F-ISO-02-accion-correctiva.md](./formatos/F-ISO-02-accion-correctiva.md) |
| F-ISO-03 | Auditoría interna | [F-ISO-03-lista-verificacion-auditoria-interna.md](./formatos/F-ISO-03-lista-verificacion-auditoria-interna.md) |
| F-ISO-04 | Revisión por la dirección | [F-ISO-04-acta-revision-direccion.md](./formatos/F-ISO-04-acta-revision-direccion.md) |

**Registros en SGMI (evidencia digital):** módulo **MOD-CALIDAD** — no conformidades (`no_conformidades`), acciones correctivas (`acciones_correctivas`), auditoría; ver [matriz-requisito-iso-sgmi.md](./matriz-requisito-iso-sgmi.md).

---

## Enlaces al proyecto SGMI

| Recurso | Ruta |
|---------|------|
| Mapa de procesos | [../01_requisitos/mapa-procesos-nivel-0.md](../01_requisitos/mapa-procesos-nivel-0.md) |
| Organigrama | [../01_requisitos/organigrama-institucional.md](../01_requisitos/organigrama-institucional.md) |
| Historias de usuario | [../01_requisitos/historias-usuario/README.md](../01_requisitos/historias-usuario/README.md) |
| Ficha de proyecto | [../01_requisitos/ficha-proyecto.md](../01_requisitos/ficha-proyecto.md) |

---

## Ciclo de mejora (PDCA)

1. **Planificar** — objetivos de calidad, riesgos, indicadores (MOD-DASH).
2. **Hacer** — operar procesos según procedimientos (MOD-DOC, MOD-PAT-TI).
3. **Verificar** — auditoría interna, indicadores, OCI (NUCLEO auditoría).
4. **Actuar** — NC/AC, revisión por la dirección, cambios al SGMI.

---

## Control de versiones

| Versión | Fecha | Cambio | Responsable |
|---------|-------|--------|-------------|
| 1.0 | 2026-06-11 | Estructura inicial SGC + alineación SGMI Fase 1 | UTIS / Gestión de calidad |
