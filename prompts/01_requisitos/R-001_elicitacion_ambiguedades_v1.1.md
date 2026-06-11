# R-001 — Refinamiento post-respuestas stakeholders

| Campo | Valor |
|-------|-------|
| **Código** | R-001 |
| **Fase** | Requisitos |
| **Versión** | v1.1 (refinado) |
| **Modelo** | Cursor Agent — 2026-06-10 |
| **Estado** | Aprobado |

---

## Prompt de refinamiento

**Prompt original:** R-001 v1  
**Resultado observado:** 22 supuestos pendientes de validación  
**Evidencia:** Respuestas stakeholders PA-01 … PA-22  
**Diagnóstico:** Contexto insuficiente en requisitos iniciales; supuestos incorrectos en firma digital, ML y numeración  

**Decisiones clave incorporadas:**
- Autenticación local; contraseña 8+ especiales; bloqueo 5/5min
- Firma digital + sello obligatorios (MVP)
- Numeración por tipo/año (Secretaría General), no código global único
- Random Forest con fichas técnicas y mantenimiento
- Patrimonio dueño; UTIS fichas y vista parcial
- APIs SIGA; simuladores SIGA/SIAF en desarrollo
- SIAF solo Presupuesto, Tesorería, Contabilidad

**Criterio de aceptación:** Matriz v1.1 y 26 HU actualizadas; `decisiones-confirmadas.md` publicado.

**Decisión final:** **Aprobado** — listo para fase diseño D-01.

## Lecciones aprendidas

- Validar numeración documental antes de asumir código único global.
- Firma digital y ML son Must en este proyecto, no Should.
- Simuladores de integración deben planificarse desde diseño.
