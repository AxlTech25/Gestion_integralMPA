# R-001 — Detección de ambigüedades y requisitos implícitos

| Campo | Valor |
|-------|-------|
| **Código** | R-001 |
| **Fase** | Requisitos |
| **Versión** | v1 |
| **Modelo** | Cursor Agent — 2026-06-10 |
| **Autor** | Equipo SGMI |
| **Estado** | Ejecutado — salida en `docs/01_requisitos/preguntas-aclaracion.md` |

---

## Prompt ejecutado

**Rol:** Actúa como analista de requisitos senior.

**Contexto:** Sistema de Gestión Municipal Integral (ERP interno) para la Municipalidad Provincial de Acobamba. Usuarios: solo personal interno. Módulos Fase 1: seguridad, gestión documentaria (S.01), inventario TI y patrimonial informático (S.05/S.06), dashboard estratégico (E.01/E.02), integraciones lectura SIGA y SIAF. Mapa de procesos Nivel 0 y organigrama oficial como referencia.

**Entrada:** Documento de especificación funcional + mapa de procesos + organigrama.

**Tarea:** Identifica requisitos funcionales explícitos, requisitos implícitos, ambigüedades, contradicciones y preguntas que debemos hacer al cliente.

**Formato:** Tabla con columnas: tipo, hallazgo, riesgo si no se aclara, pregunta sugerida, prioridad.

**Restricciones:** No inventes decisiones. Marca como supuesto todo lo que no esté confirmado.

---

## Resultado

- **22 hallazgos** documentados (PA-01 a PA-22)
- **9 prioridad Alta** — requieren validación antes de diseño detallado
- **Salida:** [preguntas-aclaracion.md](../../docs/01_requisitos/preguntas-aclaracion.md)

## Métricas

| Métrica | Valor |
|---------|-------|
| Hallazgos explícitos del doc. | 35 RF aprox. |
| Hallazgos implícitos detectados | 22 |
| Contradicciones potenciales | 3 (PA-05, PA-15, derivación org.) |

## Decisión

**Refinado** — supuestos documentados para continuar; pendiente workshop con UTIS y Secretaría General.

## Lección aprendida

Incluir en R-002 solo historias del alcance Fase 1; documentar procesos futuros en mapa de procesos sin generar historias prematuras.
