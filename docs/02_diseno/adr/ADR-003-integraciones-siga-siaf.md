# ADR-003 — Integraciones SIGA y SIAF (API + simuladores)

| Campo | Valor |
|-------|-------|
| **ADR** | ADR-003 |
| **Estado** | Aceptado |
| **Fecha** | 2026-06-10 |
| **Requisitos** | RI-SIGA-*, RI-SIAF-*, PA-16–19 |

---

## Contexto

- SIGA: importar patrimonio informático y organigrama/personal vía **API** (PA-16).
- SIAF: lectura ejecución presupuestal; detalle limitado; solo Presupuesto, Tesorería, Contabilidad (PA-18).
- Sincronización **diaria** + **manual** (PA-17).
- Sin ambientes reales aún: **simuladores** en desarrollo (PA-19).

## Decisión

### Patrón Strategy en Laravel

```php
interface SigaClientInterface {
    public function getPatrimonioInformatica(): array;
    public function getOrganigrama(): array;
    public function getPersonal(): array;
}

interface SiafClientInterface {
    public function getEjecucionPresupuestal(string $periodo): array;
}
```

Implementaciones:

| Clase | Uso |
|-------|-----|
| `SigaApiClient` | Producción con credenciales `.env` |
| `SigaSimulatorClient` | Desarrollo; JSON fixture en `storage/integrations/siga/` |
| `SiafApiClient` | Producción |
| `SiafSimulatorClient` | Desarrollo; fixture `storage/integrations/siaf/` |

Config: `INTEGRATION_SIGA_DRIVER=api|simulator`, `INTEGRATION_SIAF_DRIVER=api|simulator`.

### SIGA — endpoints esperados (contrato asumido)

| Operación | Método | Uso SGMI |
|-----------|--------|----------|
| Patrimonio TI | `GET /api/v1/patrimonio/informatica` | Upsert `equipos` |
| Áreas | `GET /api/v1/organigrama/unidades` | Upsert `unidades_organizacionales` |
| Personal activo | `GET /api/v1/personal/activo` | Sugerencias usuarios |

> Contrato real se ajustará cuando MEF/SIGA provea documentación oficial.

### SIAF — lectura

| Operación | Método | Uso SGMI |
|-----------|--------|----------|
| Ejecución consolidada | `GET /api/v1/ejecucion?periodo=YYYY-MM` | `siaf_ejecucion_snapshots` |

**Solo lectura.** Sin POST/PUT/PATCH a SIAF.

### Jobs y scheduler

| Job | Frecuencia | Manual |
|-----|------------|--------|
| `SyncSigaPatrimonioJob` | Diario 02:00 | UTIS / Patrimonio |
| `SyncSigaOrganigramaJob` | Diario 02:15 | UTIS |
| `SyncSiafEjecucionJob` | Diario 03:00 | FINANZAS_SIAF |

Cada ejecución registra `sync_logs`.

### Simuladores

Fixtures JSON representativos:

- 50 equipos informáticos con códigos patrimoniales
- Estructura gerencias/unidades sin comités en derivación
- 30 registros personal
- Snapshot presupuestal mensual con PIM y % ejecución

UI muestra badge **"Datos simulados"** cuando `es_simulacion=true`.

## Seguridad

- Credenciales API en `.env`; nunca en repositorio.
- Tokens en header; timeout 30s; reintentos 3 con backoff.
- Logs sin datos sensibles completos.

## Alternativas rechazadas

| Alternativa | Razón |
|-------------|-------|
| Import CSV manual solo | PA-16 exige API |
| Acceso directo BD SIGA | No intrusivo; política institucional |
| Escribir en SIGA/SIAF | Explícitamente prohibido |

## Verificación

- [ ] Simulator mode end-to-end en desarrollo
- [ ] Switch a API sin cambiar servicios de dominio
- [ ] `sync_logs` y snapshots correctos
