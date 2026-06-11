# Política mínima de uso de IA en desarrollo — SGMI

**Versión:** 1.0  
**Alineación:** Prompt-Centered SDLC v1.2 — Sección 8.2

---

## Alcance

Equipo de desarrollo del Sistema de Gestión Municipal Integral (SGMI) y uso de asistentes IA (Cursor, LLMs) en el ciclo de vida.

---

## Políticas

1. **Todo código generado por IA** debe pasar por revisión humana antes de integrarse al repositorio.
2. **No pegar** credenciales, datos personales de servidores, exportaciones SIGA/SIAF reales ni código propietario de terceros sin autorización.
3. **Prompts reutilizables** se versionan en `/prompts` y se asocian a métricas de calidad.
4. **Decisiones arquitectónicas** generadas con IA se registran como ADR en `/docs/02_diseno/adr`.
5. **Outputs de IA** se verifican con pruebas, linters y revisión de seguridad antes de merge.
6. **El equipo** debe comprender el código generado; no aceptar código que nadie pueda mantener.

---

## Datos prohibidos en prompts

| Categoría | Ejemplo |
|-----------|---------|
| Credenciales | passwords, API keys, tokens SIGA/SIAF |
| Datos personales reales | nombres, DNI, legajos de servidores |
| Producción | dumps de BD, logs con IPs internas sensibles |

---

## Nivel de automatización actual

**N2 — Aumentado:** la IA genera documentación y código; humano revisa y aprueba.

No pasar a N3 sin CI/CD con tests automatizados operativo.

---

## Responsables

| Rol | Responsabilidad |
|-----|-----------------|
| Líder técnico | Aprobar ADRs y arquitectura |
| UTIS | Política de acceso a ambientes SIGA/SIAF |
| Equipo dev | Revisión de código y registro de prompts |
