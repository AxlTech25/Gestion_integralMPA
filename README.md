# Sistema de Gestión Municipal Integral (SGMI)

ERP interno para la **Municipalidad Provincial de Acobamba**.

**Stack:** Laravel 12 + Vue 3 + MySQL 8 + Sanctum  
**Metodología:** Prompt-Centered SDLC v1.2  
**Estado:** Sprint 1 — scaffold + auth local

---

## Inicio rápido

1. Iniciar **Apache** y **MySQL** en XAMPP.
2. Crear BD `sgmi_mpa` (utf8mb4).
3. `php artisan migrate:fresh --seed`
4. `npm run build` o `npm run dev`
5. `php artisan serve` → http://localhost:8000

Detalle completo: [docs/03_implementacion/SETUP.md](docs/03_implementacion/SETUP.md)

**Usuario dev:** `admin.utis` / `Admin@123`

---

## Documentación

### Requisitos

- [Ficha de proyecto](docs/01_requisitos/ficha-proyecto.md)
- [Historias de usuario](docs/01_requisitos/historias-usuario/README.md)
- [Decisiones confirmadas](docs/01_requisitos/decisiones-confirmadas.md)

### Diseño

- [Modelo de datos](docs/02_diseno/modelo-datos.md)
- [Schema SQL MySQL](docs/02_diseno/schema-inicial.sql)
- [ADR-001 Stack](docs/02_diseno/adr/ADR-001-stack-arquitectura.md)

### Implementación

- [SETUP Sprint 1](docs/03_implementacion/SETUP.md)

---

## Estructura del proyecto

```
app/Http/Controllers/Api/   # API REST
app/Models/                 # Usuario, UnidadOrganizacional
app/Modules/                # Módulos lógicos (Core, …)
database/migrations/        # Schema SGMI
resources/js/               # Vue 3 SPA
docs/                       # Requisitos, diseño, setup
prompts/                    # Prompt-Centered SDLC
```

---

## API Sprint 1

| Método | Ruta | Descripción |
|--------|------|-------------|
| POST | `/api/login` | Autenticación local |
| POST | `/api/logout` | Cerrar sesión |
| GET | `/api/user` | Usuario autenticado |
