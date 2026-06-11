# Guía de instalación — Sprint 1 (XAMPP + MySQL)

## Requisitos

| Herramienta | Versión |
|-------------|---------|
| PHP | 8.2+ (XAMPP) |
| Composer | 2.x |
| Node.js | 18+ |
| MySQL | 8.0 / MariaDB (XAMPP) |
| Laravel | 12.x (instalado) |

## 1. Iniciar servicios XAMPP

1. Abrir **XAMPP Control Panel**.
2. Iniciar **Apache** y **MySQL**.

## 2. Crear base de datos

En phpMyAdmin o consola MySQL:

```sql
CREATE DATABASE sgmi_mpa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 3. Configurar entorno

El archivo `.env` ya apunta a:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sgmi_mpa
DB_USERNAME=root
DB_PASSWORD=
```

Ajustar `DB_PASSWORD` si tu MySQL tiene contraseña.

## 4. Migraciones y datos iniciales

```bash
cd c:\xampp\htdocs\Sistema_gestionMPA
php artisan migrate:fresh --seed
```

## 5. Usuario de desarrollo

| Campo | Valor |
|-------|-------|
| Usuario | `admin.utis` |
| Contraseña | `Admin@123` |

> Solo para desarrollo. Cambiar en producción.

## 6. Frontend

**Desarrollo (Vite + hot reload):**

```bash
npm run dev
```

**Producción / prueba sin Vite dev:**

```bash
npm run build
```

## 7. Ejecutar aplicación

**Opción A — Artisan (recomendado en desarrollo):**

```bash
php artisan serve
```

Abrir: http://localhost:8000

**Opción B — Apache XAMPP:**

URL: http://localhost/Sistema_gestionMPA/public

Ajustar `APP_URL` y `SANCTUM_STATEFUL_DOMAINS` en `.env` según la URL usada.

## Estructura Sprint 1

```
app/
├── Http/Controllers/Api/AuthController.php
├── Models/Usuario.php, UnidadOrganizacional.php
resources/js/          # Vue 3 SPA
database/migrations/   # Schema SGMI completo
```

## Verificación

- [ ] Login con `admin.utis` / `Admin@123`
- [ ] Panel muestra nombre del usuario
- [ ] Logout funcional
