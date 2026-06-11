# ADR-002 — Servicio ML Random Forest para predicción de fallas

| Campo | Valor |
|-------|-------|
| **ADR** | ADR-002 |
| **Estado** | Aceptado |
| **Fecha** | 2026-06-10 |
| **Requisitos** | RF-PAT-08, PA-12 |

---

## Contexto

El sistema debe calcular probabilidad de falla de equipos informáticos usando **Random Forest**, con entradas de fichas técnicas, fichas de mantenimiento, historial de incidencias y estado del equipo. Resultado: semáforo verde/amarillo/rojo (HU-PAT-03, HU-PAT-04).

## Decisión

Implementar **microservicio Python** independiente del monolito Laravel:

| Componente | Tecnología |
|------------|------------|
| API ML | **FastAPI** (Python 3.11+) |
| Algoritmo | **scikit-learn** `RandomForestClassifier` |
| Orquestación | Laravel Job → HTTP interno → FastAPI |
| Persistencia modelo | Archivo `.joblib` + metadata en `ml_modelos` |
| Resultados | Tabla `ml_predicciones` vía Laravel |

### Flujo

1. **Entrenamiento** (programado o manual UTIS): Laravel exporta dataset JSON → FastAPI `/train` → guarda modelo + métricas.
2. **Predicción batch** (diario tras sync/incidencias): Laravel POST `/predict/batch` con features por equipo → respuesta probabilidades → Laravel escribe `ml_predicciones` y asigna semáforo.

### Features (vector de entrada)

| Feature | Fuente |
|---------|--------|
| `antiguedad_anios` | `fichas_tecnicas` |
| `ram_gb`, `almacenamiento_gb` | `fichas_tecnicas` |
| `incidencias_12m` | count `incidencias` últimos 12 meses |
| `mantenimientos_correctivos_12m` | count `fichas_mantenimiento` |
| `dias_desde_ultimo_mantenimiento` | calculado |
| `estado_operativo_encoded` | `equipos.estado_operativo` |
| `tipo_equipo_encoded` | `equipos.tipo_equipo` |

### Umbrales semáforo (configurables)

| Nivel | Probabilidad falla |
|-------|-------------------|
| verde | < 0.33 |
| amarillo | 0.33 – 0.66 |
| rojo | > 0.66 |

## Alternativas evaluadas

| Alternativa | Decisión |
|-------------|----------|
| Reglas heurísticas solo PHP | Rechazada (PA-12 exige ML) |
| Rubix ML en PHP | Rechazada (ecosistema ML menor que sklearn) |
| TensorFlow / deep learning | Rechazada (datos limitados, complejidad innecesaria) |
| **sklearn Random Forest en Python** | **Aceptada** |

## API FastAPI (contrato inicial)

```http
POST /train
Body: { "dataset": [...], "version": "v1.0.0" }
Response: { "version", "metricas": { "accuracy", "f1" }, "model_path" }

POST /predict/batch
Body: { "equipos": [ { "id", "features": {...} } ], "model_version": "v1.0.0" }
Response: { "predicciones": [ { "equipo_id", "probabilidad", "factores" } ] }

GET /health
```

Comunicación solo red interna; sin exposición pública.

## Consecuencias

- Requiere Python en servidor municipal o contenedor sidecar.
- Dataset inicial pequeño: modelo con **datos simulados** en desarrollo (PA-19); reentrenar con datos reales en producción.
- Documentar limitaciones del modelo a stakeholders (probabilidad estimada, no certeza).

## Verificación

- [ ] FastAPI responde `/health`
- [ ] Train + predict batch con dataset fixture
- [ ] Laravel Job persiste `ml_predicciones` correctamente
