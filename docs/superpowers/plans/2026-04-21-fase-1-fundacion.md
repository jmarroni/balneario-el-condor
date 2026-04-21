# Fase 1 — Fundación: Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Bootstrap del proyecto Laravel 11 con stack Docker completo, auth admin funcional y CI básico. Entregable verificable: `docker compose up` levanta todo, `/login` acepta credenciales seed, `/admin` muestra dashboard placeholder según rol.

**Architecture:** Laravel 11 monolítico (Blade + Alpine + Tailwind + Vite) corriendo sobre PHP 8.3 FPM Alpine, con nginx como reverse proxy, MariaDB 11, Redis 7, worker y scheduler como contenedores separados que comparten imagen. Auth vía Laravel Breeze sin registro público, roles con `spatie/laravel-permission`.

**Tech Stack:** Laravel 11, PHP 8.3, MariaDB 11, Redis 7, Laravel Breeze (Blade), spatie/laravel-permission 6.x, Tailwind CSS 3, Alpine.js 3, Vite 5, PHPUnit, GitHub Actions.

---

## File Structure

Todo nuevo en `/home/juan/sitios/balneario-el-condor/app/` (el legacy Zend queda intocado en su sitio actual). Se resaltan los archivos que este plan crea o modifica respecto al scaffold default de `laravel/laravel`.

```
/home/juan/sitios/balneario-el-condor/
├── app/                                    ← proyecto Laravel nuevo (CWD para todos los comandos)
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── Admin/
│   │   │   │       └── DashboardController.php   ← CREATE
│   │   │   └── Middleware/
│   │   │       └── EnsureUserHasAnyRole.php      ← CREATE (opcional, usamos role: del package)
│   │   └── Providers/
│   │       └── AppServiceProvider.php             ← MODIFY
│   ├── bootstrap/
│   │   └── app.php                                ← MODIFY (registrar rutas admin.php)
│   ├── config/
│   │   ├── app.php                                ← ya existe (sin cambios)
│   │   ├── auth.php                               ← ya existe (sin cambios)
│   │   ├── cache.php                              ← ya existe (driver controlado por .env)
│   │   ├── database.php                           ← ya existe (driver controlado por .env)
│   │   ├── permission.php                         ← CREATE (publicado por Spatie)
│   │   ├── queue.php                              ← ya existe (driver controlado por .env)
│   │   └── session.php                            ← ya existe (driver controlado por .env)
│   ├── database/
│   │   ├── migrations/
│   │   │   └── <Laravel default + Breeze + Spatie>
│   │   └── seeders/
│   │       ├── DatabaseSeeder.php                 ← MODIFY
│   │       ├── RolesAndPermissionsSeeder.php      ← CREATE
│   │       └── DemoUsersSeeder.php                ← CREATE
│   ├── docker/
│   │   ├── nginx/
│   │   │   └── default.conf                       ← CREATE
│   │   └── php/
│   │       ├── Dockerfile                         ← CREATE
│   │       └── entrypoint.sh                      ← CREATE
│   ├── resources/
│   │   └── views/
│   │       ├── admin/
│   │       │   ├── dashboard.blade.php            ← CREATE
│   │       │   └── layouts/
│   │       │       └── admin.blade.php            ← CREATE
│   │       └── layouts/
│   │           └── guest.blade.php                ← ya existe (Breeze, sin cambios)
│   ├── routes/
│   │   ├── admin.php                              ← CREATE
│   │   ├── auth.php                               ← ya existe (Breeze) — MODIFY (remover register)
│   │   └── web.php                                ← MODIFY (home placeholder)
│   ├── tests/
│   │   └── Feature/
│   │       ├── Auth/
│   │       │   └── LoginTest.php                  ← ya existe (Breeze) — MODIFY (sin register)
│   │       └── Admin/
│   │           ├── DashboardAccessTest.php        ← CREATE
│   │           └── RolePermissionsTest.php        ← CREATE
│   ├── .env.example                               ← MODIFY (vars Docker + Redis)
│   ├── .dockerignore                              ← CREATE
│   ├── composer.json                              ← generado por laravel/laravel
│   ├── docker-compose.yml                         ← CREATE
│   ├── package.json                               ← generado por Breeze (con Tailwind + Alpine)
│   └── vite.config.js                             ← ya generado por Breeze
│
├── .github/
│   └── workflows/
│       └── ci.yml                                 ← CREATE
│
└── docs/superpowers/                              (spec y plans ya existen)
```

**Decisiones de estructura:**

- Laravel project root en `app/` (subdir). Todos los comandos del plan asumen `cd /home/juan/sitios/balneario-el-condor/app/` previo.
- Docker configs viven en `app/docker/` (dentro del proyecto Laravel) para que el contexto del build sea mínimo y la imagen tenga acceso directo al código.
- Rutas separadas en 3 archivos (`web.php`, `admin.php`, `auth.php` de Breeze) desde el día 1 para no refactorizar después.
- CI en `.github/workflows/ci.yml` en el root del repo (no en `app/.github/`) porque GitHub lo espera en el root del repo que aloja el código.
- Seeders `RolesAndPermissionsSeeder` y `DemoUsersSeeder` separados para poder correr solo uno u otro (`--class=...`).

---

## Task 1: Crear proyecto Laravel 11 en `app/`

**Files:**
- Create: `app/` (directorio completo con scaffold Laravel 11)
- Modify: `.gitignore` del repo raíz (si hace falta excluir `app/vendor`, `app/node_modules`)

- [ ] **Step 1: Verificar que `app/` no existe y crear proyecto Laravel**

Run desde el repo root:

```bash
cd /home/juan/sitios/balneario-el-condor
test ! -d app || { echo "ERROR: app/ ya existe"; exit 1; }
composer create-project laravel/laravel:^11.0 app --prefer-dist
```

Expected: directorio `app/` creado con scaffold Laravel 11. Último log muestra "Application ready!".

- [ ] **Step 2: Verificar versión instalada**

```bash
cd /home/juan/sitios/balneario-el-condor/app
php artisan --version
```

Expected: `Laravel Framework 11.x.y` (x ≥ 0).

- [ ] **Step 3: Ajustar `.gitignore` del proyecto Laravel**

Laravel ya trae su `.gitignore` (excluye `vendor/`, `node_modules/`, `.env`, `storage/*`). Verificar que exista:

```bash
cat /home/juan/sitios/balneario-el-condor/app/.gitignore | head -20
```

Expected: líneas como `/vendor`, `/node_modules`, `.env`, `/public/build`.

- [ ] **Step 4: Commit inicial del scaffold**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat: scaffold Laravel 11 en app/"
```

Expected: commit creado con ~100+ archivos del scaffold.

---

## Task 2: Crear Dockerfile multi-stage + entrypoint

**Files:**
- Create: `app/docker/php/Dockerfile`
- Create: `app/docker/php/entrypoint.sh`
- Create: `app/.dockerignore`

- [ ] **Step 1: Crear Dockerfile**

Create `app/docker/php/Dockerfile`:

```dockerfile
# syntax=docker/dockerfile:1.7

# ============== BASE ==============
FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
      git curl bash \
      zip unzip \
      icu-dev libzip-dev oniguruma-dev \
      libpng-dev libjpeg-turbo-dev libwebp-dev freetype-dev \
      $PHPIZE_DEPS

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j$(nproc) \
      pdo_mysql mbstring zip intl bcmath gd exif pcntl

RUN pecl install redis \
 && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# ============== DEV ==============
FROM base AS dev

RUN apk add --no-cache nodejs npm

RUN pecl install xdebug \
 && docker-php-ext-enable xdebug

# Ajustar uid del usuario www-data al host para evitar problemas de permisos (override por ARG)
ARG HOST_UID=1000
ARG HOST_GID=1000
RUN deluser www-data \
 && addgroup -g ${HOST_GID} -S www-data \
 && adduser -u ${HOST_UID} -S -G www-data www-data

USER www-data
CMD ["php-fpm"]

# ============== PROD ==============
FROM base AS prod

COPY --chown=www-data:www-data . /var/www/html

RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction \
 && php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && chown -R www-data:www-data storage bootstrap/cache

USER www-data
CMD ["php-fpm"]
```

- [ ] **Step 2: Crear entrypoint**

Create `app/docker/php/entrypoint.sh`:

```bash
#!/usr/bin/env bash
set -e

# Asegurar permisos de storage y bootstrap/cache (importantes en dev con bind mount)
if [ -d "/var/www/html/storage" ]; then
    chmod -R ug+rwx /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

exec "$@"
```

- [ ] **Step 3: Crear `.dockerignore`**

Create `app/.dockerignore`:

```
.git
.github
node_modules
vendor
storage/logs/*
storage/framework/cache/data/*
storage/framework/sessions/*
storage/framework/views/*
.env
.env.*
!.env.example
tests/coverage
.phpunit.result.cache
```

- [ ] **Step 4: Verificar sintaxis del Dockerfile**

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker build -f docker/php/Dockerfile --target dev -t balneario-app:dev .
```

Expected: build exitoso, última línea `Successfully tagged balneario-app:dev`.

- [ ] **Step 5: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/docker/ app/.dockerignore
git commit -m "feat: Dockerfile multi-stage (dev/prod) para Laravel app"
```

---

## Task 3: Crear nginx config

**Files:**
- Create: `app/docker/nginx/default.conf`

- [ ] **Step 1: Crear config nginx**

Create `app/docker/nginx/default.conf`:

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php index.html;

    client_max_body_size 20M;

    # Storage público (uploads vía Storage::url())
    location /storage/ {
        alias /var/www/html/storage/app/public/;
        expires 7d;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Assets de Vite
    location ~* \.(css|js|woff2?|svg|jpg|jpeg|png|webp|avif|ico)$ {
        expires 30d;
        try_files $uri =404;
        access_log off;
    }

    # Gzip
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;

    # Front controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_read_timeout 60;
        include fastcgi_params;
    }

    # Denegar acceso a archivos sensibles
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

- [ ] **Step 2: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/docker/nginx/
git commit -m "feat: nginx config con front controller Laravel y caching"
```

---

## Task 4: Crear `docker-compose.yml` y `.env.example`

**Files:**
- Create: `app/docker-compose.yml`
- Modify: `app/.env.example`

- [ ] **Step 1: Crear docker-compose**

Create `app/docker-compose.yml`:

```yaml
services:
  nginx:
    image: nginx:1.27-alpine
    ports:
      - "${APP_PORT:-8080}:80"
    volumes:
      - ./:/var/www/html:ro
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf:ro
    depends_on:
      - app
    restart: unless-stopped

  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: ${APP_TARGET:-dev}
      args:
        HOST_UID: ${HOST_UID:-1000}
        HOST_GID: ${HOST_GID:-1000}
    volumes:
      - ./:/var/www/html
    environment:
      - APP_ENV=${APP_ENV:-local}
      - DB_HOST=db
      - REDIS_HOST=redis
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_started
    restart: unless-stopped

  worker:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: ${APP_TARGET:-dev}
      args:
        HOST_UID: ${HOST_UID:-1000}
        HOST_GID: ${HOST_GID:-1000}
    command: php artisan queue:work --tries=3 --timeout=120 --sleep=3
    volumes:
      - ./:/var/www/html
    environment:
      - APP_ENV=${APP_ENV:-local}
      - DB_HOST=db
      - REDIS_HOST=redis
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_started
    restart: unless-stopped

  scheduler:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: ${APP_TARGET:-dev}
      args:
        HOST_UID: ${HOST_UID:-1000}
        HOST_GID: ${HOST_GID:-1000}
    command: php artisan schedule:work
    volumes:
      - ./:/var/www/html
    environment:
      - APP_ENV=${APP_ENV:-local}
      - DB_HOST=db
      - REDIS_HOST=redis
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_started
    restart: unless-stopped

  db:
    image: mariadb:11
    environment:
      MARIADB_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-root}
      MARIADB_DATABASE: ${DB_DATABASE:-balneario}
      MARIADB_USER: ${DB_USERNAME:-balneario}
      MARIADB_PASSWORD: ${DB_PASSWORD:-balneario}
    volumes:
      - mariadb_data:/var/lib/mysql
    ports:
      - "${DB_PORT:-3306}:3306"
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 10s
      timeout: 5s
      retries: 10
    restart: unless-stopped

  redis:
    image: redis:7-alpine
    command: redis-server --appendonly yes
    volumes:
      - redis_data:/data
    ports:
      - "${REDIS_PORT:-6379}:6379"
    restart: unless-stopped

volumes:
  mariadb_data:
  redis_data:
```

- [ ] **Step 2: Actualizar `.env.example`**

Modify `app/.env.example` — reemplazar contenido completo por:

```
APP_NAME="Balneario El Condor"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080

APP_PORT=8080
APP_TARGET=dev
HOST_UID=1000
HOST_GID=1000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=balneario
DB_USERNAME=balneario
DB_PASSWORD=balneario
DB_ROOT_PASSWORD=root

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_FROM_ADDRESS="no-reply@balneario-el-condor.local"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

- [ ] **Step 3: Crear `.env` local a partir del ejemplo**

```bash
cd /home/juan/sitios/balneario-el-condor/app
cp .env.example .env
```

- [ ] **Step 4: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/docker-compose.yml app/.env.example
git commit -m "feat: docker-compose con nginx+app+worker+scheduler+db+redis"
```

---

## Task 5: Levantar stack, generar APP_KEY, verificar welcome page

**Files:** (sin cambios de código; operacional)

- [ ] **Step 1: Levantar stack**

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker compose up -d --build
```

Expected: 6 contenedores levantados. `docker compose ps` muestra `nginx`, `app`, `worker`, `scheduler`, `db`, `redis` en estado `Up`.

- [ ] **Step 2: Instalar dependencias composer dentro del contenedor**

Laravel 11 ya las trae del `composer create-project`, pero lo corremos para asegurar coherencia con la imagen:

```bash
docker compose exec app composer install
```

Expected: output "Nothing to install, update or remove" o actualización sin errores.

- [ ] **Step 3: Generar APP_KEY**

```bash
docker compose exec app php artisan key:generate
```

Expected: mensaje "Application key set successfully." y línea `APP_KEY=base64:...` poblada en `.env`.

- [ ] **Step 4: Verificar que la welcome page responde**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080
```

Expected: `200`.

- [ ] **Step 5: Verificar conexión a DB y Redis**

```bash
docker compose exec app php artisan tinker --execute="echo DB::connection()->getPdo() ? 'DB OK' : 'DB FAIL'; echo PHP_EOL; echo Redis::ping() ? 'REDIS OK' : 'REDIS FAIL';"
```

Expected: `DB OK` y `REDIS OK` (+1 de Redis).

- [ ] **Step 6: Commit (solo `.env.example` si cambió, el `.env` NO se commitea)**

```bash
cd /home/juan/sitios/balneario-el-condor
git status app/.env  # debe aparecer como ignored
# Si hubo ajustes en .env.example:
# git add app/.env.example && git commit -m "chore: ajustes .env.example"
```

Expected: `app/.env` no aparece en `git status` (está ignorado por Laravel `.gitignore`).

---

## Task 6: Instalar Laravel Breeze (Blade) y remover ruta register

**Files:**
- Generados por Breeze: `app/routes/auth.php`, `app/resources/views/auth/*.blade.php`, tests, etc.
- Modify: `app/routes/auth.php` (después de instalar, comentar/remover rutas register)
- Modify: `app/tests/Feature/Auth/RegistrationTest.php` (borrar)

- [ ] **Step 1: Instalar Breeze con stack Blade**

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker compose exec app composer require laravel/breeze --dev
docker compose exec app php artisan breeze:install blade --no-interaction
```

Expected: Breeze publica vistas Blade, rutas, controllers. Mensaje final "Breeze scaffolding installed successfully."

- [ ] **Step 2: Correr migraciones para crear tabla `users`, `sessions`, etc.**

```bash
docker compose exec app php artisan migrate --no-interaction
```

Expected: listado de migraciones corridas (users, password_reset_tokens, sessions, cache, jobs).

- [ ] **Step 3: Build de assets Vite**

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

Expected: `npm install` completa y `npm run build` genera `public/build/`.

- [ ] **Step 4: Verificar login funcional**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/login
```

Expected: `200`.

- [ ] **Step 5: Remover rutas de registro en `routes/auth.php`**

Modify `app/routes/auth.php` — eliminar las líneas con `RegisteredUserController`:

Localizar y **borrar** este bloque completo (suele estar al inicio del archivo, dentro del grupo `guest`):

```php
Route::get('register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('register', [RegisteredUserController::class, 'store']);
```

Y eliminar la línea `use App\Http\Controllers\Auth\RegisteredUserController;` arriba del archivo.

- [ ] **Step 6: Borrar test de registro**

```bash
rm /home/juan/sitios/balneario-el-condor/app/tests/Feature/Auth/RegistrationTest.php
```

- [ ] **Step 7: Borrar controller de registro (opcional pero limpio)**

```bash
rm /home/juan/sitios/balneario-el-condor/app/app/Http/Controllers/Auth/RegisteredUserController.php
rm /home/juan/sitios/balneario-el-condor/app/resources/views/auth/register.blade.php
```

- [ ] **Step 8: Verificar que `/register` devuelve 404**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:8080/register
```

Expected: `404`.

- [ ] **Step 9: Correr tests de Breeze para asegurar que no rompimos nada**

```bash
docker compose exec app php artisan test --filter=Auth
```

Expected: todos los tests de Auth (login, password reset) en verde. El test de registro ya no existe.

- [ ] **Step 10: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat: instala Breeze (Blade) y remueve registro público"
```

---

## Task 7: Instalar y configurar spatie/laravel-permission

**Files:**
- Modify: `app/app/Models/User.php` (agregar trait HasRoles)
- Create: `app/config/permission.php` (publicado)
- Create: migraciones de Spatie (publicadas)

- [ ] **Step 1: Instalar paquete**

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker compose exec app composer require spatie/laravel-permission:^6.0
```

Expected: paquete instalado, sin errores.

- [ ] **Step 2: Publicar config y migraciones**

```bash
docker compose exec app php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Expected: crea `config/permission.php` y migraciones `xxxx_create_permission_tables.php`.

- [ ] **Step 3: Correr migraciones**

```bash
docker compose exec app php artisan migrate
```

Expected: tablas `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions` creadas.

- [ ] **Step 4: Agregar trait `HasRoles` al modelo User**

Modify `app/app/Models/User.php` — agregar `use Spatie\Permission\Traits\HasRoles;` arriba y `HasRoles` al trait list del modelo:

```php
<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

Nota: si Laravel 11 no generó `Sanctum\HasApiTokens`, omitir esa línea. El trait importante acá es `HasRoles`.

- [ ] **Step 5: Verificar que no rompe nada**

```bash
docker compose exec app php artisan test --filter=Auth
```

Expected: todos en verde.

- [ ] **Step 6: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat: instala spatie/laravel-permission y agrega HasRoles a User"
```

---

## Task 8: Crear seeders de roles, permisos y usuarios demo

**Files:**
- Create: `app/database/seeders/RolesAndPermissionsSeeder.php`
- Create: `app/database/seeders/DemoUsersSeeder.php`
- Modify: `app/database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Escribir test para RolesAndPermissionsSeeder**

Create `app/tests/Feature/Seeders/RolesAndPermissionsSeederTest.php`:

```php
<?php

namespace Tests\Feature\Seeders;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_three_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'editor')->exists());
        $this->assertTrue(Role::where('name', 'moderator')->exists());
    }

    public function test_admin_has_all_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::where('name', 'admin')->first();
        $totalPermissions = Permission::count();

        $this->assertGreaterThan(0, $totalPermissions);
        $this->assertSame($totalPermissions, $admin->permissions()->count());
    }

    public function test_editor_has_content_permissions_but_not_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $editor = Role::where('name', 'editor')->first();

        $this->assertTrue($editor->hasPermissionTo('news.create'));
        $this->assertTrue($editor->hasPermissionTo('events.update'));
        $this->assertFalse($editor->hasPermissionTo('users.create'));
        $this->assertFalse($editor->hasPermissionTo('roles.update'));
    }

    public function test_moderator_only_has_view_and_delete_on_moderable_modules(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $mod = Role::where('name', 'moderator')->first();

        $this->assertTrue($mod->hasPermissionTo('classifieds.view'));
        $this->assertTrue($mod->hasPermissionTo('classifieds.delete'));
        $this->assertFalse($mod->hasPermissionTo('classifieds.create'));
        $this->assertFalse($mod->hasPermissionTo('news.view'));
    }
}
```

- [ ] **Step 2: Correr test — debe fallar**

```bash
docker compose exec app php artisan test tests/Feature/Seeders/RolesAndPermissionsSeederTest.php
```

Expected: FAIL con "Class 'Database\Seeders\RolesAndPermissionsSeeder' not found".

- [ ] **Step 3: Implementar RolesAndPermissionsSeeder**

Create `app/database/seeders/RolesAndPermissionsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    private const MODULES = [
        'news', 'news_categories',
        'events', 'event_registrations',
        'lodgings', 'venues', 'rentals',
        'classifieds', 'classified_contacts',
        'service_providers', 'recipes', 'tides',
        'gallery', 'nearby_places', 'useful_info',
        'pages', 'surveys', 'survey_responses',
        'newsletter_subscribers', 'newsletter_campaigns',
        'contact_messages', 'advertising_contacts',
        'users', 'roles',
    ];

    private const ACTIONS = ['view', 'create', 'update', 'delete'];

    private const MODERABLE_MODULES = [
        'classifieds', 'contact_messages',
        'event_registrations', 'newsletter_subscribers',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        foreach (self::MODULES as $module) {
            foreach (self::ACTIONS as $action) {
                Permission::firstOrCreate([
                    'name'       => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Rol admin: todos los permisos
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Rol editor: todo menos users.* y roles.*
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorPerms = Permission::where('name', 'not like', 'users.%')
            ->where('name', 'not like', 'roles.%')
            ->get();
        $editor->syncPermissions($editorPerms);

        // Rol moderator: solo view + delete en módulos moderables
        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $modPerms = collect(self::MODERABLE_MODULES)
            ->flatMap(fn ($m) => ["{$m}.view", "{$m}.delete"])
            ->map(fn ($p) => Permission::where('name', $p)->first())
            ->filter()
            ->all();
        $moderator->syncPermissions($modPerms);
    }
}
```

- [ ] **Step 4: Correr test — debe pasar**

```bash
docker compose exec app php artisan test tests/Feature/Seeders/RolesAndPermissionsSeederTest.php
```

Expected: 4 tests PASS.

- [ ] **Step 5: Escribir test para DemoUsersSeeder**

Create `app/tests/Feature/Seeders/DemoUsersSeederTest.php`:

```php
<?php

namespace Tests\Feature\Seeders;

use App\Models\User;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoUsersSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_one_user_per_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(DemoUsersSeeder::class);

        $admin = User::where('email', 'admin@balneario.local')->first();
        $editor = User::where('email', 'editor@balneario.local')->first();
        $moderator = User::where('email', 'moderator@balneario.local')->first();

        $this->assertNotNull($admin);
        $this->assertNotNull($editor);
        $this->assertNotNull($moderator);

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($editor->hasRole('editor'));
        $this->assertTrue($moderator->hasRole('moderator'));
    }

    public function test_demo_passwords_are_hashed_and_match_known_value(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(DemoUsersSeeder::class);

        $admin = User::where('email', 'admin@balneario.local')->first();
        $this->assertTrue(Hash::check('password', $admin->password));
    }
}
```

- [ ] **Step 6: Correr test — debe fallar**

```bash
docker compose exec app php artisan test tests/Feature/Seeders/DemoUsersSeederTest.php
```

Expected: FAIL con "Class 'Database\Seeders\DemoUsersSeeder' not found".

- [ ] **Step 7: Implementar DemoUsersSeeder**

Create `app/database/seeders/DemoUsersSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Demo',     'email' => 'admin@balneario.local',     'role' => 'admin'],
            ['name' => 'Editor Demo',    'email' => 'editor@balneario.local',    'role' => 'editor'],
            ['name' => 'Moderator Demo', 'email' => 'moderator@balneario.local', 'role' => 'moderator'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
        }
    }
}
```

- [ ] **Step 8: Correr test — debe pasar**

```bash
docker compose exec app php artisan test tests/Feature/Seeders/DemoUsersSeederTest.php
```

Expected: 2 tests PASS.

- [ ] **Step 9: Modificar DatabaseSeeder para correr ambos**

Modify `app/database/seeders/DatabaseSeeder.php` — reemplazar el método `run` por:

```php
public function run(): void
{
    $this->call([
        RolesAndPermissionsSeeder::class,
        DemoUsersSeeder::class,
    ]);
}
```

- [ ] **Step 10: Correr seeding completo en DB real**

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Expected: migraciones corridas de cero y seeders ejecutados sin error. En output aparece el call a ambos seeders.

- [ ] **Step 11: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat: seeders de roles, permisos y usuarios demo (admin/editor/moderator)"
```

---

## Task 9: Configurar Redis drivers (cache, queue, sessions)

**Files:**
- Modify: `app/config/cache.php` (ya tiene redis, verificar default)
- Modify: `app/config/queue.php`
- Modify: `app/config/session.php`

Laravel 11 lee el driver de `.env` (`CACHE_DRIVER`, `QUEUE_CONNECTION`, `SESSION_DRIVER`), que ya pusimos en `redis` en Task 4. Acá validamos que efectivamente funciona end-to-end.

- [ ] **Step 1: Verificar que `.env` tiene las vars correctas**

```bash
docker compose exec app grep -E "^(CACHE_DRIVER|QUEUE_CONNECTION|SESSION_DRIVER)=" .env
```

Expected:
```
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

- [ ] **Step 2: Escribir test de integración Redis**

Create `app/tests/Feature/RedisIntegrationTest.php`:

```php
<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisIntegrationTest extends TestCase
{
    public function test_redis_ping_responds_truthy(): void
    {
        // phpredis returns true, Predis returns 'PONG' — acepto ambos.
        $this->assertTrue((bool) Redis::ping());
    }

    public function test_cache_writes_and_reads_from_redis(): void
    {
        Cache::put('test:key', 'test-value', 60);
        $this->assertSame('test-value', Cache::get('test:key'));
        Cache::forget('test:key');
    }

    public function test_queue_default_connection_is_redis(): void
    {
        $this->assertSame('redis', config('queue.default'));
    }

    public function test_session_driver_is_redis(): void
    {
        $this->assertSame('redis', config('session.driver'));
    }
}
```

- [ ] **Step 3: Correr test**

```bash
docker compose exec app php artisan test tests/Feature/RedisIntegrationTest.php
```

Expected: 4 tests PASS.

- [ ] **Step 4: Verificar que el worker está procesando cola**

```bash
docker compose ps worker
docker compose logs --tail=10 worker
```

Expected: contenedor `worker` en estado `Up`, logs no muestran errores fatales (puede decir "No jobs available" si está idle — es OK).

- [ ] **Step 5: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/tests/
git commit -m "test: verificación de integración Redis (cache, queue, sessions)"
```

---

## Task 10: Crear rutas admin + dashboard placeholder + middleware

**Files:**
- Create: `app/routes/admin.php`
- Modify: `app/bootstrap/app.php` (registrar rutas admin)
- Create: `app/app/Http/Controllers/Admin/DashboardController.php`
- Create: `app/resources/views/admin/layouts/admin.blade.php`
- Create: `app/resources/views/admin/dashboard.blade.php`
- Modify: `app/routes/web.php` (home placeholder)

- [ ] **Step 1: Escribir test para acceso al dashboard admin**

Create `app/tests/Feature/Admin/DashboardAccessTest.php`:

```php
<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_editor_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('editor');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_moderator_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('moderator');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_user_without_any_role_is_forbidden(): void
    {
        $user = User::factory()->create();
        // sin rol

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }
}
```

- [ ] **Step 2: Correr test — debe fallar**

```bash
docker compose exec app php artisan test tests/Feature/Admin/DashboardAccessTest.php
```

Expected: FAIL (ruta `/admin` no existe).

- [ ] **Step 3: Crear DashboardController**

Create `app/app/Http/Controllers/Admin/DashboardController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard');
    }
}
```

- [ ] **Step 4: Crear layout admin**

Create `app/resources/views/admin/layouts/admin.blade.php`:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('admin.dashboard') }}" class="font-semibold">
                {{ config('app.name') }} · Admin
            </a>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                    ({{ auth()->user()->getRoleNames()->join(', ') }})
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </main>
</body>
</html>
```

- [ ] **Step 5: Crear dashboard view**

Create `app/resources/views/admin/dashboard.blade.php`:

```blade
@extends('admin.layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">
            Panel administrativo de Balneario El Cóndor.
        </p>
        <p class="text-sm text-gray-500 mt-4">
            Placeholder — los CRUDs de cada módulo llegan en Fase 4.
        </p>
    </div>
@endsection
```

- [ ] **Step 6: Crear `routes/admin.php`**

Create `app/routes/admin.php`:

```php
<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin|editor|moderator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
    });
```

- [ ] **Step 7: Registrar alias del middleware `role` en `bootstrap/app.php`**

Modify `app/bootstrap/app.php` — agregar alias del middleware de Spatie dentro de `withMiddleware`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
```

No tocamos `withRouting`: cargamos admin.php vía `require` desde `web.php` (Step 8) para que herede el middleware group `web` y la sesión funcione correctamente con `auth`.

- [ ] **Step 8: Cargar admin.php desde web.php + home placeholder**

Modify `app/routes/web.php` — reemplazar contenido completo por:

```php
<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

require __DIR__.'/admin.php';
```

El `require __DIR__.'/auth.php'` ya lo trae Breeze; verificar que esté (si no está, agregarlo también).

```bash
grep "require.*auth.php" /home/juan/sitios/balneario-el-condor/app/routes/web.php
```

Expected: una línea con `require __DIR__.'/auth.php';`. Si no aparece, agregarla.

- [ ] **Step 9: Correr tests de acceso al dashboard**

```bash
docker compose exec app php artisan test tests/Feature/Admin/DashboardAccessTest.php
```

Expected: 5 tests PASS.

- [ ] **Step 10: Verificar manualmente en browser**

Levantar sitio y loguear:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Abrir `http://localhost:8080/login`, loguear con `admin@balneario.local` / `password`, debe redirigir a `/dashboard` (o `/admin` según config Breeze). Verificar que `/admin` carga el dashboard con header "Dashboard".

Nota: Breeze redirige por default a `/dashboard` tras login. Para redirigir a `/admin`, modificar en `app/app/Http/Controllers/Auth/AuthenticatedSessionController.php` el método `store`, cambiando `return redirect()->intended(route('dashboard', absolute: false));` por `return redirect()->intended(route('admin.dashboard', absolute: false));`.

- [ ] **Step 11: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat: ruta /admin con middleware auth+role y dashboard placeholder"
```

---

## Task 11: Configurar GitHub Actions CI

**Files:**
- Create: `.github/workflows/ci.yml` (en el root del repo, no en `app/`)

- [ ] **Step 1: Crear workflow CI**

Create `.github/workflows/ci.yml`:

```yaml
name: CI

on:
  push:
    branches: [master, main]
  pull_request:
    branches: [master, main]

jobs:
  test:
    name: PHP Tests
    runs-on: ubuntu-latest

    services:
      mariadb:
        image: mariadb:11
        env:
          MARIADB_ROOT_PASSWORD: root
          MARIADB_DATABASE: balneario_test
          MARIADB_USER: balneario
          MARIADB_PASSWORD: balneario
        ports:
          - 3306:3306
        options: >-
          --health-cmd="healthcheck.sh --connect --innodb_initialized"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=10

      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379
        options: >-
          --health-cmd "redis-cli ping"
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    defaults:
      run:
        working-directory: app

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_mysql, mbstring, zip, intl, bcmath, gd, redis
          coverage: none

      - name: Cache composer dependencies
        uses: actions/cache@v4
        with:
          path: app/vendor
          key: ${{ runner.os }}-composer-${{ hashFiles('app/composer.lock') }}
          restore-keys: ${{ runner.os }}-composer-

      - name: Install composer dependencies
        run: composer install --no-interaction --prefer-dist --optimize-autoloader

      - name: Prepare .env for testing
        run: |
          cp .env.example .env
          php artisan key:generate
        env:
          DB_HOST: 127.0.0.1
          DB_DATABASE: balneario_test
          REDIS_HOST: 127.0.0.1

      - name: Run migrations
        run: php artisan migrate --no-interaction
        env:
          DB_HOST: 127.0.0.1
          DB_DATABASE: balneario_test
          DB_USERNAME: balneario
          DB_PASSWORD: balneario
          REDIS_HOST: 127.0.0.1

      - name: Run PHPUnit tests
        run: php artisan test
        env:
          DB_HOST: 127.0.0.1
          DB_DATABASE: balneario_test
          DB_USERNAME: balneario
          DB_PASSWORD: balneario
          REDIS_HOST: 127.0.0.1

  lint:
    name: PHP Lint
    runs-on: ubuntu-latest

    defaults:
      run:
        working-directory: app

    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          tools: cs2pr

      - name: Install composer dependencies
        run: composer install --no-interaction --prefer-dist

      - name: Check syntax errors
        run: find app -type f -name "*.php" ! -path "*/vendor/*" -exec php -l {} \;
        working-directory: ${{ github.workspace }}
```

- [ ] **Step 2: Verificar sintaxis YAML**

```bash
cd /home/juan/sitios/balneario-el-condor
python3 -c "import yaml; yaml.safe_load(open('.github/workflows/ci.yml'))"
```

Expected: sin output (YAML válido).

- [ ] **Step 3: Commit y push (si el remote existe)**

```bash
cd /home/juan/sitios/balneario-el-condor
git add .github/
git commit -m "ci: GitHub Actions con PHPUnit + lint PHP"
# git push (si remote configurado)
```

- [ ] **Step 4: (Opcional) Verificar que CI corre**

Si el repo tiene remote en GitHub, abrir la pestaña Actions del repo y verificar que el workflow corre en verde. Si no hay remote aún, este paso se pospone.

---

## Task 12: README + smoke test final

**Files:**
- Create: `app/README.md`

- [ ] **Step 1: Crear README**

Create `app/README.md`:

```markdown
# Balneario El Cóndor — Laravel

Migración del sitio de turismo desde Zend Framework 1 a Laravel 11.
Ver `docs/superpowers/specs/2026-04-21-migracion-laravel-design.md` para el
diseño completo.

## Stack

- Laravel 11, PHP 8.3
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

Sitio en [http://localhost:8080](http://localhost:8080).

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
```

- [ ] **Step 2: Ejecutar smoke test completo**

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker compose down -v
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install
docker compose exec app npm run build
docker compose exec app php artisan test
```

Expected: stack levanta, migraciones corren, seeders crean roles y usuarios, `npm run build` genera assets, todos los tests PASS.

- [ ] **Step 3: Verificación manual en browser**

Abrir `http://localhost:8080`. Ver welcome page Laravel. Ir a `http://localhost:8080/login`, ingresar `admin@balneario.local` / `password`. Navegar a `http://localhost:8080/admin`. Ver dashboard "Dashboard" con nombre y rol del usuario en el header.

- [ ] **Step 4: Commit final**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/README.md
git commit -m "docs: README del proyecto Laravel con setup y credenciales demo"
```

---

## Criterios de aceptación de Fase 1

- [ ] `docker compose up -d` desde `app/` levanta 6 contenedores sin errores.
- [ ] `http://localhost:8080` responde 200 con la welcome page de Laravel.
- [ ] `http://localhost:8080/login` acepta `admin@balneario.local` / `password` y redirige a dashboard.
- [ ] `http://localhost:8080/admin` carga el dashboard placeholder para usuarios con cualquier rol (admin/editor/moderator).
- [ ] `http://localhost:8080/register` devuelve 404 (no hay registro público).
- [ ] `docker compose exec app php artisan test` pasa al 100%.
- [ ] `docker compose exec app php artisan test tests/Feature/RedisIntegrationTest.php` pasa.
- [ ] `docker compose exec app php artisan migrate:fresh --seed` ejecuta sin error y crea los 3 usuarios demo con sus roles.
- [ ] `.github/workflows/ci.yml` ejecuta tests en GitHub Actions (si el repo tiene remote).
- [ ] Workflow del worker procesa la cola Redis sin errores en logs.
- [ ] Scheduler está corriendo `schedule:work` sin errores.

Con todo esto verde, se puede pasar al **Plan 2 — Schema** (Fase 2 del spec).
