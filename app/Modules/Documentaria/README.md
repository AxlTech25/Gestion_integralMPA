# Módulo MOD-DOC — Gestión documentaria

## Implementado

- Modelos: `Expediente`, `TipoDocumental`, `ExpedienteMovimiento`, `Documento`, `ExpedienteAdjunto`, `TramiteConstancia`
- Servicio: `ExpedienteService` (registrar, derivar, devolver, recepcionar)
- API REST bajo `/api/expedientes`, `/api/tipos-documentales`
- Frontend: bandeja, registro y trazabilidad conectados al API
- Seeder: `TiposDocumentalesSeeder`

## Flujo

1. **Registrar** — numeración por tipo+año, movimiento `registro`, adjunto opcional
2. **Derivar** — destino libre; estado `por_recepcionar` si el tipo exige recepción
3. **Recepcionar** — acuse digital; estado `en_tramite`
4. **Devolver** — observación obligatoria; retorno automático al remitente inmediato
