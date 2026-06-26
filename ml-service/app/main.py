import os
from contextlib import asynccontextmanager
from typing import Annotated

from fastapi import Depends, FastAPI, Header, HTTPException
from pydantic import BaseModel, Field

from app.model_store import ModelStore

API_TOKEN = os.getenv("ML_API_TOKEN", "")
store = ModelStore()


def verify_token(authorization: Annotated[str | None, Header()] = None) -> None:
    if not API_TOKEN:
        return
    if not authorization or not authorization.startswith("Bearer "):
        raise HTTPException(status_code=401, detail="Token requerido")
    token = authorization.removeprefix("Bearer ").strip()
    if token != API_TOKEN:
        raise HTTPException(status_code=403, detail="Token inválido")


class TrainRequest(BaseModel):
    version: str = Field(default="v1.0.0", max_length=20)
    dataset: list[dict] = Field(min_length=10)


class PredictBatchRequest(BaseModel):
    model_version: str = Field(default="v1.0.0", max_length=20)
    equipos: list[dict] = Field(min_length=1)


def bootstrap_model() -> None:
    if store.exists():
        store.load()
        return

    import json
    from pathlib import Path

    fixture = os.getenv("BOOTSTRAP_DATASET", "fixtures/dataset_demo.json")
    path = Path(fixture)
    if not path.is_file():
        path = Path(__file__).resolve().parent.parent / "fixtures" / "dataset_demo.json"

    if path.is_file():
        data = json.loads(path.read_text(encoding="utf-8"))
        dataset = data.get("dataset") or data
        version = data.get("version", os.getenv("ML_MODEL_VERSION", "v1.0.0"))
        store.train(dataset, version)


@asynccontextmanager
async def lifespan(_app: FastAPI):
    bootstrap_model()
    yield


app = FastAPI(
    title="SGMI ML Service",
    description="Random Forest — predicción de fallas patrimonio TI (Municipalidad Acobamba)",
    version="1.0.0",
    lifespan=lifespan,
)


@app.get("/health")
def health():
    loaded = store.model is not None or store.load()
    return {
        "status": "ok" if loaded else "degraded",
        "model_loaded": loaded,
        "model_version": store.meta.get("version"),
        "model_path": str(store.model_path),
    }


@app.post("/train", dependencies=[Depends(verify_token)])
def train(body: TrainRequest):
    try:
        meta = store.train(body.dataset, body.version)
    except ValueError as e:
        raise HTTPException(status_code=422, detail=str(e)) from e

    return {
        "version": body.version,
        "metricas": meta.get("metricas"),
        "model_path": str(store.model_path),
    }


@app.post("/predict/batch", dependencies=[Depends(verify_token)])
def predict_batch(body: PredictBatchRequest):
    try:
        predicciones = store.predict_batch(body.equipos)
    except RuntimeError as e:
        raise HTTPException(status_code=503, detail=str(e)) from e

    return {"predicciones": predicciones}


@app.post("/train/demo", dependencies=[Depends(verify_token)])
def train_demo():
    import json
    from pathlib import Path

    fixture = Path(__file__).resolve().parent.parent / "fixtures" / "dataset_demo.json"
    data = json.loads(fixture.read_text(encoding="utf-8"))
    dataset = data.get("dataset") or data
    version = data.get("version", "v1.0.0")
    meta = store.train(dataset, version)
    return {"version": version, "metricas": meta.get("metricas")}
