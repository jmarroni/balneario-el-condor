# Balneario El Cóndor — Laravel

Migración del sitio de turismo desde Zend Framework 1 a Laravel 11.
Ver `docs/superpowers/specs/2026-04-21-migracion-laravel-design.md` para el
diseño completo.

## Stack

- Laravel 11, PHP 8.4
- MariaDB 11, Redis 7
- Blade + Tailwind + Alpine.js + Vite
- Laravel Breeze (auth) + spatie/laravel-permission (roles)

## Setup (dev)

Prerequisitos: Docker, Docker Compose.

```bash
cd app
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run build
```

Sitio en [http://localhost:8080](http://localhost:8080) (o el puerto que tengas en `APP_PORT`).

## Credenciales demo

| Rol | Email | Password |
|---|---|---|
| admin | admin@balneario.local | password |
| editor | editor@balneario.local | password |
| moderator | moderator@balneario.local | password |

## Comandos útiles

```bash
docker compose exec app php artisan test              # tests
docker compose exec app php artisan migrate:fresh --seed  # reset DB
docker compose exec app npm run dev                   # Vite HMR
docker compose logs -f app                            # logs Laravel
docker compose logs -f worker                         # logs queue worker
```

## Estructura

- `app/` — código Laravel (controllers, models, views, etc.)
- `docker/` — Dockerfile, nginx config, entrypoint
- `routes/web.php` — rutas públicas
- `routes/admin.php` — rutas panel admin (prefijo `/admin`, middleware auth+role)
- `routes/auth.php` — login, logout, password reset (Breeze)
- `docs/superpowers/` — spec y plans de implementación
