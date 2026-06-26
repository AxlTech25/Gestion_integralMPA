# SGMI ML Service — FastAPI + Random Forest

Microservicio de predicción de fallas para equipos informáticos (ADR-002).  
Desplegable en **Railway**; consumido por Laravel vía `ML_SERVICE_URL`.

## Endpoints

| Método | Ruta | Auth | Descripción |
|--------|------|------|-------------|
| GET | `/health` | No | Estado y versión del modelo |
| POST | `/train` | Bearer | Entrena con dataset JSON |
| POST | `/train/demo` | Bearer | Entrena con fixture demo |
| POST | `/predict/batch` | Bearer | Predicción batch |

## Variables de entorno

| Variable | Descripción | Default |
|----------|-------------|---------|
| `ML_API_TOKEN` | Token compartido con Laravel | (vacío = sin auth en dev) |
| `ML_MODEL_VERSION` | Versión activa | `v1.0.0` |
| `MODEL_PATH` | Ruta modelo `.joblib` | `/data/model.joblib` |
| `MODEL_META_PATH` | Metadata JSON | `/data/model_meta.json` |
| `BOOTSTRAP_DATASET` | Fixture entrenamiento inicial | `fixtures/dataset_demo.json` |
| `PORT` | Puerto HTTP (Railway) | `8000` |

## Desarrollo local

```bash
cd ml-service
python -m venv .venv
.venv\Scripts\activate          # Windows PowerShell
pip install -r requirements.txt
set ML_API_TOKEN=dev-token-local
python -m uvicorn app.main:app --reload --port 8001
```

> **Windows:** use `python -m uvicorn` (no escriba solo `uvicorn` si no está en PATH).  
> **Puerto:** si Laravel usa `php artisan serve` en **8000**, levante el ML en **8001** y configure `ML_SERVICE_URL=http://127.0.0.1:8001`.

Probar:

```bash
curl http://localhost:8000/health
curl -X POST http://localhost:8000/predict/batch -H "Authorization: Bearer dev-token-local" -H "Content-Type: application/json" -d "{\"equipos\":[{\"id\":1,\"features\":{\"antiguedad_anios\":5,\"ram_gb\":8,\"almacenamiento_gb\":512,\"incidencias_12m\":2,\"mantenimientos_correctivos_12m\":1,\"dias_desde_ultimo_mantenimiento\":200,\"estado_operativo\":\"operativo\",\"tipo_equipo\":\"pc\"}}]}"
```

## Despliegue en Railway

1. Crear proyecto en [railway.app](https://railway.app)
2. **New Service** → Deploy from GitHub repo (o CLI)
3. Configurar **Root Directory** = `ml-service`
4. Añadir **Volume** montado en `/data` (persistir modelo entre deploys)
5. Variables en Railway:

   ```
   ML_API_TOKEN=<generar-secreto-largo>
   ML_MODEL_VERSION=v1.0.0
   MODEL_PATH=/data/model.joblib
   MODEL_META_PATH=/data/model_meta.json
   ```

6. Tras el deploy, verificar: `https://<tu-servicio>.up.railway.app/health`

7. En Laravel (`.env`):

   ```
   ML_SERVICE_URL=https://<tu-servicio>.up.railway.app
   ML_API_TOKEN=<mismo-secreto>
   ML_MODEL_VERSION=v1.0.0
   ```

8. Entrenar con datos reales (opcional):

   ```bash
   php artisan sgmi:ml-train
   php artisan sgmi:ml-predict
   ```

## Conexión Laravel

- `MlPredictionService` envía `POST /predict/batch` con Bearer token
- Sin `ML_SERVICE_URL` → simulador PHP (desarrollo offline)
- Comando `sgmi:ml-train` exporta dataset desde BD y llama `/train`

## Notas

- El modelo demo se entrena automáticamente al arrancar si no existe en `MODEL_PATH`
- Reentrenar con datos reales cuando haya suficiente historial de incidencias
- La probabilidad es **estimada**, no certeza operativa (documentar a usuarios)
