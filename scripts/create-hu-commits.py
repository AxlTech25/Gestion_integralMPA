#!/usr/bin/env python3
"""Create one git commit per user story with traceability documentation."""
from __future__ import annotations

import importlib.util
import json
import re
import subprocess
from datetime import datetime, timezone
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
SCRIPTS = Path(__file__).resolve().parent
TRAZA_DIR = ROOT / "docs" / "03_implementacion" / "trazabilidad"
COMMITS_JSON = TRAZA_DIR / "commits.json"

HU_SOURCE = {
    "SEC": "HU-seguridad-y-organigrama.md",
    "ORG": "HU-seguridad-y-organigrama.md",
    "DOC": "HU-gestion-documentaria.md",
    "PAT": "HU-patrimonial-ti.md",
    "DASH": "HU-dashboard.md",
    "INT": "HU-integraciones.md",
}


def load_matriz_data():
    spec = importlib.util.spec_from_file_location(
        "fill_matriz", SCRIPTS / "fill-matriz-doble-entrada.py"
    )
    mod = importlib.util.module_from_spec(spec)
    spec.loader.exec_module(mod)
    return mod.HISTORIAS, mod.PROMPTS


def source_file(hu: str) -> str:
    mod = hu.split("-")[1]
    return HU_SOURCE.get(mod, "README.md")


def run_git(*args: str, check: bool = True) -> subprocess.CompletedProcess:
    return subprocess.run(
        ["git", "-C", str(ROOT), *args],
        capture_output=True,
        text=True,
        check=check,
    )


def render_markdown(h: dict, prompts: dict) -> str:
    p = prompts[h["prompt"]]
    src = source_file(h["hu"])
    return f"""# {h['hu']} — {h['title']}

Registro de trazabilidad Prompt-Centered SDLC — SGMI-MPA.

| Campo | Valor |
|-------|-------|
| **ID función** | F-{h['hu'].replace('HU-', '')} |
| **Módulo** | {h['module']} |
| **Requisitos** | {h['rf']} |
| **Estado** | {h['status']} |
| **Commit implementación** | `{h['commit']}` |
| **Ubicación principal** | `{h['location']}` |
| **Pruebas** | `{h['test']}` |
| **Prompt** | {p['id']} (`{p['file']}`) |
| **Versión prompt** | {p['version']} |

## Historia de usuario

Ver [../../01_requisitos/historias-usuario/{src}](../../01_requisitos/historias-usuario/{src}).

## Prompt de desarrollo

> {h['prompt_text']}

## Observaciones

{h['notes']}

---

*El commit de trazabilidad de esta historia es el commit de Git que introduce este archivo.*
"""


def commit_message(h: dict) -> str:
    prefix = "feat" if h["status"] == "Implementado" else "docs"
    return f"{prefix}({h['hu']}): {h['title']}"


def build_registry_from_log(historias: list) -> dict:
    log = run_git(
        "log",
        "--format=%H|%h|%s|%aI",
        "--reverse",
    ).stdout.strip()
    registry: dict = {}
    pattern = re.compile(r"^(feat|docs)\((HU-[A-Z]+-\d+)\):")
    for line in log.splitlines():
        sha, short, subject, date = line.split("|", 3)
        match = pattern.match(subject)
        if not match:
            continue
        hu = match.group(2)
        impl = next((h["commit"] for h in historias if h["hu"] == hu), "")
        registry[hu] = {
            "sha": sha,
            "short": short,
            "message": subject,
            "date": date,
            "impl_commit": impl,
        }
    return registry


def render_readme(registry: dict, historias: list) -> str:
    lines = [
        "# Trazabilidad por historia de usuario",
        "",
        "Un commit de Git por historia de usuario (Prompt-Centered SDLC v1.2).",
        "",
        f"**Generado:** {datetime.now(timezone.utc).strftime('%Y-%m-%d %H:%M UTC')}",
        "",
        "| HU | Título | Commit trazabilidad | Commit implementación | Estado |",
        "|----|--------|---------------------|----------------------|--------|",
    ]
    for h in historias:
        hu = h["hu"]
        row = registry.get(hu, {})
        short = row.get("short", "—")
        lines.append(
            f"| [{hu}](./{hu}.md) | {h['title']} | `{short}` | `{h['commit']}` | {h['status']} |"
        )
    lines.extend(
        [
            "",
            "## Regenerar matriz Excel",
            "",
            "```bash",
            "python scripts/fill-matriz-doble-entrada.py",
            "```",
            "",
        ]
    )
    return "\n".join(lines)


def main() -> None:
    historias, prompts = load_matriz_data()
    TRAZA_DIR.mkdir(parents=True, exist_ok=True)
    created = 0
    skipped = 0

    for h in historias:
        hu = h["hu"]
        md_path = TRAZA_DIR / f"{hu}.md"
        rel = md_path.relative_to(ROOT).as_posix()

        if md_path.exists():
            tracked = run_git("ls-files", "--error-unmatch", rel, check=False)
            if tracked.returncode == 0:
                print(f"SKIP {hu} (archivo ya versionado)")
                skipped += 1
                continue

        md_path.write_text(render_markdown(h, prompts), encoding="utf-8")
        msg = commit_message(h)
        run_git("add", rel)
        run_git("commit", "-m", msg)
        short = run_git("rev-parse", "--short", "HEAD").stdout.strip()
        created += 1
        print(f"OK  {hu} -> {short}  {msg}")

    registry = build_registry_from_log(historias)
    COMMITS_JSON.write_text(
        json.dumps(registry, indent=2, ensure_ascii=False) + "\n",
        encoding="utf-8",
    )
    readme = TRAZA_DIR / "README.md"
    readme.write_text(render_readme(registry, historias), encoding="utf-8")

    print(f"\nCreados: {created}, omitidos: {skipped}")
    print(f"Registry: {len(registry)} entradas -> {COMMITS_JSON}")
    print("Ejecuta: python scripts/fill-matriz-doble-entrada.py")


if __name__ == "__main__":
    main()
