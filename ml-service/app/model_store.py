import json
import os
from pathlib import Path

import joblib
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, f1_score
from sklearn.model_selection import train_test_split

from app.features import FEATURE_NAMES, encode_features, heuristic_label

DEFAULT_MODEL_PATH = os.getenv("MODEL_PATH", "/data/model.joblib")
DEFAULT_META_PATH = os.getenv("MODEL_META_PATH", "/data/model_meta.json")


class ModelStore:
    def __init__(self, model_path: str | None = None, meta_path: str | None = None):
        self.model_path = Path(model_path or DEFAULT_MODEL_PATH)
        self.meta_path = Path(meta_path or DEFAULT_META_PATH)
        self._model: RandomForestClassifier | None = None
        self._meta: dict = {}

    @property
    def model(self) -> RandomForestClassifier | None:
        return self._model

    @property
    def meta(self) -> dict:
        return self._meta

    def exists(self) -> bool:
        return self.model_path.is_file()

    def load(self) -> bool:
        if not self.exists():
            return False
        self._model = joblib.load(self.model_path)
        if self.meta_path.is_file():
            self._meta = json.loads(self.meta_path.read_text(encoding="utf-8"))
        return True

    def save(self, model: RandomForestClassifier, meta: dict) -> None:
        self.model_path.parent.mkdir(parents=True, exist_ok=True)
        joblib.dump(model, self.model_path)
        self.meta_path.write_text(json.dumps(meta, indent=2), encoding="utf-8")
        self._model = model
        self._meta = meta

    def train(self, dataset: list[dict], version: str) -> dict:
        if len(dataset) < 10:
            raise ValueError("Se requieren al menos 10 registros para entrenar.")

        rows = []
        labels = []
        for item in dataset:
            features = item.get("features") or item
            label = item.get("label")
            if label is None:
                label = heuristic_label(features)
            rows.append(encode_features(features))
            labels.append(int(label))

        x_train, x_test, y_train, y_test = train_test_split(
            rows, labels, test_size=0.2, random_state=42, stratify=labels if len(set(labels)) > 1 else None
        )

        model = RandomForestClassifier(
            n_estimators=100,
            max_depth=8,
            random_state=42,
            class_weight="balanced",
        )
        model.fit(x_train, y_train)

        y_pred = model.predict(x_test) if x_test else y_train
        y_true = y_test if x_test else y_train

        metricas = {
            "accuracy": round(float(accuracy_score(y_true, y_pred)), 4),
            "f1": round(float(f1_score(y_true, y_pred, zero_division=0)), 4),
            "muestras": len(dataset),
            "features": FEATURE_NAMES,
        }

        meta = {
            "version": version,
            "algoritmo": "random_forest",
            "parametros": {"n_estimators": 100, "max_depth": 8},
            "metricas": metricas,
        }

        self.save(model, meta)
        return meta

    def predict_batch(self, equipos: list[dict]) -> list[dict]:
        if self._model is None and not self.load():
            raise RuntimeError("Modelo no entrenado. Ejecute POST /train primero.")

        predicciones = []
        for item in equipos:
            equipo_id = item["id"]
            raw = item.get("features") or item
            vector = [encode_features(raw)]
            proba = self._model.predict_proba(vector)[0]
            # Clase 1 = falla
            if len(proba) > 1:
                probabilidad = float(proba[1])
            else:
                probabilidad = float(proba[0])

            predicciones.append(
                {
                    "equipo_id": equipo_id,
                    "probabilidad": round(min(0.99, max(0.01, probabilidad)), 4),
                    "factores": {name: vector[0][i] for i, name in enumerate(FEATURE_NAMES)},
                }
            )

        return predicciones
