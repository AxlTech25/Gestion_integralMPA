"""Codificación de features alineada con MlFeatureService (Laravel)."""

ESTADOS = {"operativo": 0, "reparacion": 1, "almacen": 2, "baja": 3}
TIPOS = {"pc": 0, "servidor": 1, "impresora": 2, "red": 3, "otro": 4}

FEATURE_NAMES = [
    "antiguedad_anios",
    "ram_gb",
    "almacenamiento_gb",
    "incidencias_12m",
    "mantenimientos_correctivos_12m",
    "dias_desde_ultimo_mantenimiento",
    "estado_operativo_encoded",
    "tipo_equipo_encoded",
]


def encode_features(raw: dict) -> list[float]:
    estado = raw.get("estado_operativo", "operativo")
    tipo = raw.get("tipo_equipo", "otro")

    return [
        float(raw.get("antiguedad_anios") or 0),
        float(raw.get("ram_gb") or 0),
        float(raw.get("almacenamiento_gb") or 0),
        float(raw.get("incidencias_12m") or 0),
        float(raw.get("mantenimientos_correctivos_12m") or 0),
        float(raw.get("dias_desde_ultimo_mantenimiento") or 365),
        float(ESTADOS.get(estado, 0)),
        float(TIPOS.get(tipo, 4)),
    ]


def heuristic_label(raw: dict) -> int:
    """Etiqueta sintética para entrenamiento demo (1 = riesgo alto de falla)."""
    score = 0.0
    score += min(1.0, float(raw.get("incidencias_12m") or 0) * 0.15)
    score += min(1.0, float(raw.get("mantenimientos_correctivos_12m") or 0) * 0.12)
    score += min(1.0, float(raw.get("antiguedad_anios") or 0) * 0.05)
    if raw.get("estado_operativo") == "reparacion":
        score += 0.4
    if float(raw.get("dias_desde_ultimo_mantenimiento") or 0) > 180:
        score += 0.15
    return 1 if score >= 0.45 else 0
