# Fase 7 — Deploy producción + CI/CD

**Fuente:** spec §9 Stack Docker (diferencias dev/prod) + extensiones operativas.
**Prerequisitos:** Fase 6 mergeada (384 tests, API + mails funcionando).
**Meta:** dejar el proyecto listo para deploy en VPS con HTTPS, backups, observabilidad básica, CI verde y un runbook reproducible.

## Estado inicial

Ya existe:
- `app/docker/php/Dockerfile` con stages base/dev/prod (prod hace `composer install --no-dev` + Artisan caches, pero **no compila assets Vite**).
- `app/docker/nginx/default.conf` (config dev).
- `app/docker-compose.yml` con servicios dev (php, nginx, db, redis, worker, scheduler + legacy_db profile).
- `.github/workflows/ci.yml` con tests PHPUnit + lint syntax.

Falta todo lo prod-specific.

## Decisiones

- **HTTPS:** Traefik delante de nginx, con ACME automático (Let's Encrypt). Alternativa documentada: Caddy.
- **Imagen única:** un Dockerfile, dos targets (`dev`/`prod`) — `prod` ya existe; lo extendemos.
- **Compose dev/prod:** `docker-compose.yml` queda dev. `docker-compose.prod.yml` agrega overrides para prod (target=prod, env_file, sin bind mounts, Traefik labels).
- **Secrets:** `.env.production.example` versionado, `.env.production` real fuera del repo. CI inyecta vía GitHub Secrets.
- **Backups:** scripts bash en `ops/` ejecutables vía cron del host (no dentro de containers).
- **Monitoring básico:** Laravel Pulse opcional + Sentry stub. Healthcheck `/up` ya existe (Laravel 11 default).

---

## Task 1: Dockerfile prod con assets compilados + opcache

**Archivos:**
- Modify: `app/docker/php/Dockerfile`
- Create: `app/docker/php/php-prod.ini`
- Modify: `app/docker/php/entrypoint.sh` — branch para prod
- Create: `app/.dockerignore`

### Step 1: Stage `node-builder`

Agregar al Dockerfile entre `base` y `prod`:

```dockerfile
# ============== NODE BUILDER ==============
FROM node:20-alpine AS node-builder

WORKDIR /build
COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund

COPY resources/ resources/
COPY vite.config.* tailwind.config.* postcss.config.* ./
RUN npm run build
```

### Step 2: Stage `prod` extendido

Reemplazar el stage `prod` actual con:

```dockerfile
# ============== PROD ==============
FROM base AS prod

# OPcache + tuning prod
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/zz-prod.ini

# Código fuente (sin .git ni node_modules ni storage)
COPY --chown=www-data:www-data . /var/www/html

# Composer install no-dev con autoload optimizado
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction \
 && composer dump-autoload --classmap-authoritative

# Assets compilados desde el node-builder
COPY --from=node-builder --chown=www-data:www-data /build/public/build /var/www/html/public/build

# Caches de Artisan (config + route + view + event)
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan event:cache

# Permisos finales
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
 && find /var/www/html/storage -type d -exec chmod 775 {} \; \
 && find /var/www/html/bootstrap/cache -type d -exec chmod 775 {} \;

USER www-data

# Healthcheck PHP-FPM ping (escucha en 9000 default)
HEALTHCHECK --interval=30s --timeout=5s --retries=3 \
  CMD php-fpm-healthcheck || exit 1

CMD ["php-fpm"]
```

### Step 3: php-prod.ini

Crear `app/docker/php/php-prod.ini`:

```ini
; OPcache tuning para prod
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=192
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0    ; en prod no chequear mtime, solo invalidar al deploy
opcache.preload=/var/www/html/storage/framework/preload.php
opcache.preload_user=www-data
opcache.jit_buffer_size=64M
opcache.jit=tracing

; Limits razonables
memory_limit=256M
upload_max_filesize=20M
post_max_size=24M
max_execution_time=60
max_input_vars=3000

; Logging
log_errors=On
error_log=/dev/stderr
display_errors=Off
display_startup_errors=Off

; Sesiones (Laravel maneja sus sesiones, esto es default PHP)
session.cookie_secure=On
session.cookie_httponly=On
session.cookie_samesite=Lax
```

### Step 4: .dockerignore

Crear `app/.dockerignore`:

```
.git
.gitignore
.env
.env.*
!.env.example
!.env.production.example
node_modules
vendor
storage/logs/*.log
storage/framework/cache/data/*
storage/framework/sessions/*
storage/framework/views/*
public/storage
public/build
public/docs
.scribe
tests
phpunit.xml
.phpunit.cache
.idea
.vscode
*.md
docker-compose*.yml
docker
!docker/php
!docker/nginx
README.md
```

(Ojo: `tests` y `phpunit.xml` se excluyen del build prod — los tests corren en CI antes del deploy, no en la imagen prod.)

### Step 5: entrypoint con branch prod

Modificar `app/docker/php/entrypoint.sh` para detectar APP_ENV:

```bash
#!/bin/sh
set -e

if [ "${APP_ENV}" = "production" ]; then
    # En prod: solo ejecutar migrations si MIGRATE_ON_BOOT=true (off por default)
    if [ "${MIGRATE_ON_BOOT:-false}" = "true" ]; then
        php artisan migrate --force --no-interaction
    fi
    php artisan config:cache > /dev/null
    php artisan route:cache > /dev/null
    php artisan view:cache > /dev/null
    php artisan event:cache > /dev/null
else
    # Dev: comportamiento existente (no cache, posible composer install si vendor falta)
    if [ ! -d /var/www/html/vendor ]; then
        composer install --no-interaction
    fi
fi

exec "$@"
```

### Step 6: Verificar build

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker build -f docker/php/Dockerfile --target prod -t balneario:prod-test .
```

La imagen debe construir sin errores. Tamaño esperado < 500MB.

### Commit

```bash
git add app/docker/ app/.dockerignore
git commit -m "feat(deploy): Dockerfile prod multi-stage con node-builder + opcache + caches"
```

---

## Task 2: docker-compose.prod.yml + nginx prod

**Archivos:**
- Create: `app/docker-compose.prod.yml`
- Create: `app/docker/nginx/prod.conf`
- Create: `app/docker/nginx/snippets/security-headers.conf`
- Create: `app/docker/nginx/snippets/cache.conf`

### docker-compose.prod.yml

Override de prod sobre el dev. Se usa con `docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d`.

```yaml
services:
  app:
    build:
      context: .
      dockerfile: docker/php/Dockerfile
      target: prod
    image: balneario:${APP_VERSION:-latest}
    restart: unless-stopped
    env_file:
      - .env.production
    environment:
      APP_ENV: production
      APP_DEBUG: "false"
    volumes:
      # Solo storage (uploads + logs persistentes)
      - app_storage:/var/www/html/storage/app
      - app_logs:/var/www/html/storage/logs
    # Sin bind mount del código — viene en la imagen
    # Sin port expose — solo nginx accede

  worker:
    image: balneario:${APP_VERSION:-latest}
    restart: unless-stopped
    env_file:
      - .env.production
    command: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
    depends_on:
      app:
        condition: service_started

  scheduler:
    image: balneario:${APP_VERSION:-latest}
    restart: unless-stopped
    env_file:
      - .env.production
    command: php artisan schedule:work
    depends_on:
      app:
        condition: service_started

  nginx:
    restart: unless-stopped
    volumes:
      - ./docker/nginx/prod.conf:/etc/nginx/conf.d/default.conf:ro
      - ./docker/nginx/snippets:/etc/nginx/snippets:ro
      - app_storage:/var/www/html/storage/app:ro     # para servir storage/legacy
      - app_public:/var/www/html/public:ro            # build assets + docs
    labels:
      # Traefik (opcional, comentado si no se usa)
      - "traefik.enable=true"
      - "traefik.http.routers.balneario.rule=Host(`elcondor.gob.ar`)"
      - "traefik.http.routers.balneario.entrypoints=websecure"
      - "traefik.http.routers.balneario.tls.certresolver=letsencrypt"
      - "traefik.http.services.balneario.loadbalancer.server.port=80"

  db:
    restart: unless-stopped
    env_file:
      - .env.production
    # En prod NO publicar el puerto al host (solo intra-network)
    ports: []

  redis:
    restart: unless-stopped
    ports: []
    command: redis-server --appendonly yes --requirepass ${REDIS_PASSWORD}

volumes:
  app_storage:
  app_logs:
  app_public:
```

**Nota crítica:** el volumen `app_public` necesita poblarse desde la imagen al primer arranque. Hacerlo con un init container o con un volume mount inverso es complicado. **Solución más simple:** servir los assets directamente desde el container `app` mediante alias en el nginx, sin volumen compartido. Ver Task 2 Step 3.

### nginx prod.conf

Crear `app/docker/nginx/prod.conf`:

```nginx
server {
    listen 80;
    server_name _;
    root /var/www/html/public;
    index index.php;
    charset utf-8;

    # Logs
    access_log /var/log/nginx/access.log combined buffer=16k flush=10s;
    error_log /var/log/nginx/error.log warn;

    # Security headers globales
    include /etc/nginx/snippets/security-headers.conf;

    # Healthcheck pasthrough a Laravel /up
    location = /up {
        try_files $uri /index.php?$query_string;
    }

    # Robots y sitemap servidos rápido
    location = /robots.txt {
        try_files $uri /index.php?$query_string;
        access_log off;
    }
    location = /sitemap.xml {
        try_files $uri /index.php?$query_string;
    }

    # Assets compilados (Vite build)
    location ^~ /build/ {
        access_log off;
        include /etc/nginx/snippets/cache.conf;
        add_header Cache-Control "public, max-age=31536000, immutable";
        try_files $uri =404;
    }

    # Storage público (legacy media + uploads)
    location ^~ /storage/ {
        access_log off;
        include /etc/nginx/snippets/cache.conf;
        add_header Cache-Control "public, max-age=2592000";  # 30 días
        try_files $uri =404;
    }

    # Bloquear acceso directo a /docs en prod (basic auth o env-gate via Laravel)
    # NOTA: la protección real se hace en el middleware Laravel (Task 5).
    # Acá solo servimos el HTML estático.
    location ^~ /docs/ {
        try_files $uri $uri/ /index.php?$query_string;
        access_log off;
    }

    # Imágenes / fonts comunes
    location ~* \.(jpg|jpeg|png|gif|webp|svg|ico|woff2?|ttf|otf)$ {
        access_log off;
        add_header Cache-Control "public, max-age=2592000";
        try_files $uri =404;
    }

    # Front controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_param HTTPS on;            # Traefik termina TLS, propaga a Laravel
        fastcgi_hide_header X-Powered-By;
        fastcgi_read_timeout 60s;
        client_max_body_size 24m;
    }

    # Bloquear archivos sensibles
    location ~ /\.(?!well-known) {
        deny all;
    }
    location ~ /\.env {
        deny all;
    }
}
```

### Snippets

Crear `app/docker/nginx/snippets/security-headers.conf`:

```nginx
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header Permissions-Policy "camera=(), microphone=(), geolocation=(self)" always;
add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
# CSP — relajada por Leaflet + Google Fonts + Alpine inline. Endurecer en iteración.
add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com data:; img-src 'self' data: https: blob:; connect-src 'self' https://api.open-meteo.com; frame-src https://www.youtube.com https://www.youtube-nocookie.com;" always;
```

Crear `app/docker/nginx/snippets/cache.conf`:

```nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_proxied any;
gzip_types text/plain text/css text/xml text/javascript application/javascript application/json application/xml image/svg+xml;
```

### Servir storage del container app

Ajustar el bind del nginx — en lugar de volumen compartido, usar `nginx` con `proxy_pass` al app... NO. La solución idiomática: el directorio `public/` se sirve desde el filesystem del container `app` montado en read-only por el `nginx`.

**Opción más simple para prod:**
- El container `nginx` corre con un volumen `app_public` que se inicializa copiando desde el container `app` al primer arranque.
- Implementar con un `init container` o un `entrypoint.sh` del nginx que `cp -r` desde `/var/www/html/public/*` (montado vía `app_storage_init`) a `/usr/share/nginx/html`.

**Solución preferida:** correr `nginx` y `app` en el mismo POD/network y que `nginx` haga `root /var/www/html/public` con un volumen que también monte el app:

```yaml
nginx:
  volumes_from:
    - app:ro     # Deprecated en compose v3 — usar el alternativo
```

Compose v3 ya no soporta `volumes_from`. Alternativa: ambos usan el mismo named volume:

```yaml
app:
  volumes:
    - app_public:/var/www/html/public

nginx:
  volumes:
    - app_public:/var/www/html/public:ro
```

Con un init que copie. Más simple aún: **mantener el código fuente en el contenedor app y que nginx haga proxy total** (proxy_pass) en lugar de servir static. Pero eso pierde la performance de nginx para assets.

**Decisión final pragmática:** los assets del build se sirven desde nginx que tiene los archivos via `volumes_from` reemplazado por `mount path` compartido en un named volume populado por el container `app` con un script init.

Implementar esto en Task 8 (deploy runbook). Por ahora documentar la decisión.

### Commit

```bash
git add app/docker-compose.prod.yml app/docker/nginx/prod.conf app/docker/nginx/snippets/
git commit -m "feat(deploy): docker-compose.prod.yml + nginx prod con security headers + cache"
```

---

## Task 3: .env.production.example + secrets

**Archivos:**
- Create: `app/.env.production.example`
- Modify: `.gitignore` (ya debería ignorar .env.production, verificar)

Crear `app/.env.production.example`:

```dotenv
# ===== App =====
APP_NAME="Balneario El Cóndor"
APP_ENV=production
APP_KEY=                         # generar con php artisan key:generate
APP_DEBUG=false
APP_URL=https://elcondor.gob.ar
APP_VERSION=v1.0.0
APP_LOCALE=es
APP_FALLBACK_LOCALE=en

# ===== Database (interna) =====
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=balneario_prod
DB_USERNAME=balneario
DB_PASSWORD=                     # secret

# MariaDB root (necesario para init container)
MYSQL_ROOT_PASSWORD=             # secret
MYSQL_DATABASE=balneario_prod
MYSQL_USER=balneario
MYSQL_PASSWORD=                  # secret = DB_PASSWORD

# ===== Redis =====
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=                  # secret
REDIS_CLIENT=phpredis
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# ===== Mail (Resend) =====
MAIL_MAILER=resend
MAIL_FROM_ADDRESS="no-reply@elcondor.gob.ar"
MAIL_FROM_NAME="Balneario El Cóndor"
ADMIN_NOTIFICATION_EMAIL=turismo@elcondor.gob.ar
RESEND_KEY=                      # secret: re_xxxx desde resend.com

# ===== Logging =====
LOG_CHANNEL=stack
LOG_LEVEL=warning
LOG_STACK=daily,errorlog

# ===== Session =====
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.elcondor.gob.ar

# ===== Sanctum =====
SANCTUM_STATEFUL_DOMAINS=elcondor.gob.ar
SESSION_SAME_SITE=lax

# ===== Open-Meteo (sin key, no secret) =====
# (no env vars — hardcoded en SyncWeatherCommand)

# ===== Sentry (opcional) =====
SENTRY_LARAVEL_DSN=
SENTRY_TRACES_SAMPLE_RATE=0.1

# ===== Migration on boot =====
MIGRATE_ON_BOOT=false           # true solo en deploys con cambios de schema

# ===== Scribe docs =====
SCRIBE_DOCS_ENABLED=false        # false en prod por default
SCRIBE_DOCS_BASIC_AUTH_USER=     # opcional, si SCRIBE_DOCS_ENABLED=true
SCRIBE_DOCS_BASIC_AUTH_PASS=

# ===== Backup (consumido por scripts ops/) =====
BACKUP_DIR=/var/backups/balneario
BACKUP_RETENTION_DAYS=14
```

### .gitignore

Verificar en `app/.gitignore` que estén ignorados:
```
.env
.env.production
.env.local
```

(El `.env.example` y `.env.production.example` SÍ se versionan.)

### Commit

```bash
git add app/.env.production.example
git commit -m "feat(deploy): .env.production.example con todos los secrets documentados"
```

---

## Task 4: GitHub Actions — CI mejorado + build de imagen

**Archivos:**
- Modify: `.github/workflows/ci.yml` — agregar Pint check + Vite build + ApiContractTest
- Create: `.github/workflows/release.yml` — build + push imagen Docker en tags

### CI mejorado

Agregar al `ci.yml` después del job `test`:

```yaml
  pint:
    name: Pint format check
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: app
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - run: composer install --no-interaction --prefer-dist
      - run: ./vendor/bin/pint --test

  assets:
    name: Vite build
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: app
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: 20
          cache: npm
          cache-dependency-path: app/package-lock.json
      - run: npm ci
      - run: npm run build
      - uses: actions/upload-artifact@v4
        with:
          name: build-assets
          path: app/public/build
          retention-days: 7
```

### release.yml

Crear `.github/workflows/release.yml`:

```yaml
name: Release

on:
  push:
    tags: ['v*.*.*']
  workflow_dispatch:

jobs:
  docker:
    name: Build & Push Docker image
    runs-on: ubuntu-latest
    permissions:
      contents: read
      packages: write
    steps:
      - uses: actions/checkout@v4

      - name: Login GHCR
        uses: docker/login-action@v3
        with:
          registry: ghcr.io
          username: ${{ github.actor }}
          password: ${{ secrets.GITHUB_TOKEN }}

      - uses: docker/setup-buildx-action@v3

      - name: Build & push
        uses: docker/build-push-action@v5
        with:
          context: ./app
          file: ./app/docker/php/Dockerfile
          target: prod
          push: true
          tags: |
            ghcr.io/${{ github.repository }}/app:${{ github.ref_name }}
            ghcr.io/${{ github.repository }}/app:latest
          cache-from: type=gha
          cache-to: type=gha,mode=max
```

### Commit

```bash
git add .github/workflows/
git commit -m "ci: agregar Pint check + Vite build + workflow de release con imagen GHCR"
```

---

## Task 5: Hardening prod — proteger /docs + healthcheck + Pulse stub

**Archivos:**
- Modify: `app/config/scribe.php` — env-gate
- Create: `app/app/Http/Middleware/RestrictDocsInProd.php`
- Modify: `app/routes/web.php` — registrar middleware en ruta de docs si aplica
- Optional: install `laravel/pulse`

### Step 1: Proteger /docs en prod

Opción A — middleware que requiere basic auth si `APP_ENV=production`:

Crear `app/app/Http/Middleware/RestrictDocsInProd.php`:

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictDocsInProd
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! app()->environment('production')) {
            return $next($request);
        }

        if (! config('scribe.docs_enabled', false)) {
            abort(404);
        }

        // Basic auth si configurado
        $user = config('scribe.docs_basic_auth_user');
        $pass = config('scribe.docs_basic_auth_pass');

        if ($user && $pass) {
            $authUser = $request->getUser();
            $authPass = $request->getPassword();

            if ($authUser !== $user || ! hash_equals($pass, $authPass ?? '')) {
                return response('Unauthorized', 401, [
                    'WWW-Authenticate' => 'Basic realm="API Docs"',
                ]);
            }
        }

        return $next($request);
    }
}
```

Registrar alias en `bootstrap/app.php`:
```php
$middleware->alias([
    // ... existentes
    'restrict.docs' => \App\Http\Middleware\RestrictDocsInProd::class,
]);
```

En `routes/web.php`, agregar antes del fallback:
```php
Route::middleware('restrict.docs')->get('/docs/{path?}', function ($path = 'index.html') {
    $file = public_path('docs/' . $path);
    if (! is_file($file)) abort(404);
    return response()->file($file);
})->where('path', '.*');
```

Esto reemplaza el serving estático de nginx por uno gateado. En dev sigue accesible directo desde nginx.

### Step 2: Config

En `app/config/scribe.php` agregar al final:
```php
'docs_enabled' => env('SCRIBE_DOCS_ENABLED', false),
'docs_basic_auth_user' => env('SCRIBE_DOCS_BASIC_AUTH_USER'),
'docs_basic_auth_pass' => env('SCRIBE_DOCS_BASIC_AUTH_PASS'),
```

### Step 3: Healthcheck

Verificar `/up` (Laravel 11 lo registra automáticamente vía `withRouting(health: '/up')`). Smoke:
```bash
curl -s -o /dev/null -w "%{http_code}\n" http://localhost:18081/up
# 200
```

### Step 4: Tests

Crear `app/tests/Feature/Hardening/DocsRestrictionTest.php`:
- test_docs_accessible_in_dev (env=local → 200)
- test_docs_returns_404_in_prod_when_disabled (env=production + SCRIBE_DOCS_ENABLED=false → 404)
- test_docs_requires_basic_auth_when_enabled_in_prod (env=production + creds set + sin auth → 401)
- test_docs_serves_when_basic_auth_correct

Usar `Config::set('app.env', 'production')` o swap del env helper para testear branches.

### Step 5: Pulse (opcional, marcar TODO)

Instalación:
```bash
docker compose exec -T app composer require laravel/pulse
docker compose exec -T app php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
docker compose exec -T app php artisan migrate
```

Configurar dashboard en `/admin/pulse` con auth admin. Documentar en runbook como pasos opcionales — NO ejecutar en este task (mantenerlo lean).

### Commit

```bash
git add app/
git commit -m "feat(deploy): hardening prod — /docs gateado por env + basic auth + healthcheck"
```

---

## Task 6: Backups — mysqldump + storage tar

**Archivos:**
- Create: `ops/backup.sh`
- Create: `ops/restore.sh`
- Create: `ops/cron.example`
- Create: `ops/README.md`

### backup.sh

Crear `ops/backup.sh`:

```bash
#!/usr/bin/env bash
# Backup de Balneario El Cóndor — DB + storage
# Uso: ops/backup.sh
# Requiere: docker, gzip, tar

set -euo pipefail

# Cargar .env.production (DB_DATABASE, DB_USERNAME, DB_PASSWORD, BACKUP_DIR, BACKUP_RETENTION_DAYS)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/../app"
ENV_FILE="$APP_DIR/.env.production"

if [ ! -f "$ENV_FILE" ]; then
    echo "ERROR: $ENV_FILE no existe" >&2
    exit 1
fi

set -a
source "$ENV_FILE"
set +a

BACKUP_DIR="${BACKUP_DIR:-/var/backups/balneario}"
RETENTION="${BACKUP_RETENTION_DAYS:-14}"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_PATH="$BACKUP_DIR/$TIMESTAMP"

mkdir -p "$BACKUP_PATH"

echo "→ Backup DB → $BACKUP_PATH/db.sql.gz"
docker compose -f "$APP_DIR/docker-compose.yml" -f "$APP_DIR/docker-compose.prod.yml" \
    exec -T db sh -c \
    "mariadb-dump --single-transaction --routines --triggers --events \
     -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}" \
    | gzip > "$BACKUP_PATH/db.sql.gz"

echo "→ Backup storage → $BACKUP_PATH/storage.tar.gz"
docker compose -f "$APP_DIR/docker-compose.yml" -f "$APP_DIR/docker-compose.prod.yml" \
    exec -T app sh -c "tar -czf - -C /var/www/html storage/app" \
    > "$BACKUP_PATH/storage.tar.gz"

# Tamaño
echo "→ Tamaños:"
du -sh "$BACKUP_PATH"/*

# Cleanup viejos
echo "→ Eliminando backups > $RETENTION días"
find "$BACKUP_DIR" -maxdepth 1 -type d -name "20*" -mtime +"$RETENTION" -exec rm -rf {} \;

echo "✓ Backup OK: $BACKUP_PATH"
```

### restore.sh

```bash
#!/usr/bin/env bash
# Restore: ops/restore.sh /var/backups/balneario/20260425-120000

set -euo pipefail

if [ -z "${1:-}" ]; then
    echo "Uso: $0 <backup-path>"
    echo "Ej: $0 /var/backups/balneario/20260425-120000"
    exit 1
fi

BACKUP_PATH="$1"
[ -d "$BACKUP_PATH" ] || { echo "ERROR: $BACKUP_PATH no existe"; exit 1; }

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/../app"
ENV_FILE="$APP_DIR/.env.production"

set -a; source "$ENV_FILE"; set +a

echo "⚠ Esto va a SOBRESCRIBIR la DB y storage. Confirmar [yes/no]:"
read -r CONFIRM
[ "$CONFIRM" = "yes" ] || { echo "Abortado"; exit 1; }

echo "→ Restaurando DB"
gunzip -c "$BACKUP_PATH/db.sql.gz" | docker compose -f "$APP_DIR/docker-compose.yml" -f "$APP_DIR/docker-compose.prod.yml" \
    exec -T db sh -c "mariadb -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE}"

echo "→ Restaurando storage"
docker compose -f "$APP_DIR/docker-compose.yml" -f "$APP_DIR/docker-compose.prod.yml" \
    exec -T app sh -c "rm -rf /var/www/html/storage/app && tar -xzf - -C /var/www/html" \
    < "$BACKUP_PATH/storage.tar.gz"

echo "✓ Restore OK"
```

### cron.example

```cron
# Backup diario 03:00 AM
0 3 * * * /home/balneario/balneario-el-condor/ops/backup.sh >> /var/log/balneario-backup.log 2>&1
```

### README.md

`ops/README.md`:

```markdown
# Operaciones

Scripts de backup, restore y deploy para Balneario El Cóndor en producción.

## Backups

`./backup.sh` genera un par de archivos en `$BACKUP_DIR/<timestamp>/`:
- `db.sql.gz` — mariadb-dump comprimido
- `storage.tar.gz` — `storage/app/` completo (uploads, legacy media, logs)

Retención automática: archivos más viejos que `$BACKUP_RETENTION_DAYS` se eliminan.

Cron sugerido: ver `cron.example`.

## Restore

`./restore.sh /path/to/backup` restaura DB y storage. Pide confirmación.

## Variables

Las variables se leen de `app/.env.production`:
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `BACKUP_DIR` (default `/var/backups/balneario`)
- `BACKUP_RETENTION_DAYS` (default 14)
```

### Permisos

```bash
chmod +x ops/backup.sh ops/restore.sh
```

### Commit

```bash
git add ops/
git commit -m "feat(deploy): scripts ops para backup mysqldump + storage tar con retención"
```

---

## Task 7: Deploy runbook + smoke script

**Archivos:**
- Create: `docs/RUNBOOK.md`
- Create: `ops/smoke.sh`
- Create: `ops/deploy.sh`

### deploy.sh

```bash
#!/usr/bin/env bash
# Deploy: ops/deploy.sh [version]
# Pulls latest, builds, migrates, swaps containers.

set -euo pipefail

VERSION="${1:-latest}"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$SCRIPT_DIR/../app"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml -f $APP_DIR/docker-compose.prod.yml"

cd "$APP_DIR/.."

echo "→ Backup pre-deploy"
"$SCRIPT_DIR/backup.sh"

echo "→ Pulling code"
git fetch --tags
git checkout "$VERSION"

echo "→ Building app image (target=prod)"
$COMPOSE build app

echo "→ Migrate (con app temporal)"
$COMPOSE run --rm app php artisan migrate --force --no-interaction

echo "→ Swap containers"
$COMPOSE up -d --no-deps --force-recreate app worker scheduler nginx

echo "→ Esperando 10s + smoke"
sleep 10
"$SCRIPT_DIR/smoke.sh"

echo "✓ Deploy $VERSION OK"
```

### smoke.sh

```bash
#!/usr/bin/env bash
# Smoke prod: verifica las rutas críticas

set -euo pipefail

URL="${APP_URL:-https://elcondor.gob.ar}"
FAILS=0

check() {
    local path="$1"
    local expected="$2"
    local code
    code=$(curl -sk -o /dev/null -w "%{http_code}" "${URL}${path}")
    if [ "$code" = "$expected" ]; then
        echo "✓ $path → $code"
    else
        echo "✗ $path → $code (esperaba $expected)" >&2
        FAILS=$((FAILS+1))
    fi
}

check "/up" 200
check "/" 200
check "/novedades" 200
check "/eventos" 200
check "/hospedajes" 200
check "/mareas" 200
check "/sitemap.xml" 200
check "/robots.txt" 200
check "/admin" 302         # redirect a /login
check "/api/v1/me" 401     # sin auth
check "/api/v1/contact" 405  # GET sobre POST endpoint

if [ "$FAILS" -gt 0 ]; then
    echo "→ $FAILS smoke check(s) fallaron" >&2
    exit 1
fi
echo "→ Todo verde"
```

### RUNBOOK.md

```markdown
# Runbook — Balneario El Cóndor

Operaciones de producción.

## Stack

- VPS Linux con Docker Engine 24+ y Docker Compose v2.
- Traefik (o Caddy) como reverse proxy con HTTPS automático.
- Servicios via Docker Compose: `app`, `nginx`, `db`, `redis`, `worker`, `scheduler`.

## Deploy inicial

1. Clonar repo:
   ```
   git clone https://github.com/<owner>/balneario-el-condor.git
   cd balneario-el-condor
   ```

2. Generar `.env.production` desde el example y completar secrets:
   ```
   cp app/.env.production.example app/.env.production
   nano app/.env.production
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml run --rm app php artisan key:generate --show
   # pegar la clave en APP_KEY
   ```

3. Levantar la red Traefik (si aplica) — ver `docs/traefik-setup.md`.

4. Build + arranque:
   ```
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml build
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml up -d
   ```

5. Migrar la DB:
   ```
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml exec app php artisan migrate --force
   ```

6. Seedear roles + admin user:
   ```
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml exec app php artisan db:seed --class=RolesAndPermissionsSeeder --force
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml exec app php artisan tinker
   # Crear user admin manualmente
   ```

7. ETL (una sola vez, si no se hizo):
   ```
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml --profile etl up -d legacy_db
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml exec app php artisan etl:all
   docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml --profile etl down
   ```

8. Smoke:
   ```
   ./ops/smoke.sh
   ```

## Deploy de actualización

```
./ops/deploy.sh v1.2.3
```

(El script hace backup, pull, build, migrate, swap, smoke.)

Rollback:
```
git checkout v1.2.2
./ops/deploy.sh v1.2.2
# O restaurar backup completo:
./ops/restore.sh /var/backups/balneario/<timestamp>
```

## Cron

Configurar en el host (no en container):
```
0 3 * * * /opt/balneario/ops/backup.sh >> /var/log/balneario-backup.log 2>&1
```

El scheduler de Laravel (`weather:sync` cada 30 min, etc.) corre en el container `scheduler`.

## Logs

- App: `docker compose logs app -f`
- Worker: `docker compose logs worker -f`
- Nginx: `docker compose logs nginx -f`
- Storage: `docker compose exec app tail -f storage/logs/laravel.log`

## Monitoring

- Healthcheck: `https://elcondor.gob.ar/up` → 200 OK
- (Opcional) Laravel Pulse en `/admin/pulse` (requiere instalación, ver Plan 7 Task 5).
- (Opcional) Sentry: setear `SENTRY_LARAVEL_DSN` en `.env.production`.

## Troubleshooting

### Newsletter no envía mails

```
docker compose exec app php artisan queue:failed
docker compose exec app php artisan queue:retry all
```

Verificar `RESEND_KEY` válido en `.env.production`.

### DB lenta

```
docker compose exec app php artisan db:show
docker compose exec app php artisan db:monitor
```

Ver `EXPLAIN` de queries lentos. Considerar índices adicionales.

### Imagen pesada

```
docker images balneario:latest
docker history balneario:latest
```

Si supera 600MB, revisar `.dockerignore`.
```

### Permisos

```bash
chmod +x ops/deploy.sh ops/smoke.sh
```

### Commit final

```bash
git add ops/ docs/RUNBOOK.md
git commit -m "feat(deploy): runbook prod + scripts deploy.sh y smoke.sh"
```

---

## Criterios de aceptación de Fase 7

- [ ] `docker build -f app/docker/php/Dockerfile --target prod -t balneario:prod .` arma OK.
- [ ] Imagen prod < 500MB.
- [ ] `docker-compose.prod.yml` levanta con `.env.production` válido.
- [ ] CI passes en cada push: tests + Pint + Vite build + lint.
- [ ] Release workflow: tag `v*.*.*` builda y pushea a GHCR.
- [ ] `/docs` da 404 en prod sin `SCRIBE_DOCS_ENABLED=true`. Con basic auth correcto, sirve.
- [ ] `/up` devuelve 200.
- [ ] `ops/backup.sh` genera `db.sql.gz` + `storage.tar.gz` con timestamp.
- [ ] `ops/restore.sh` restaura backup con confirmación.
- [ ] `ops/smoke.sh` verifica 11 rutas críticas.
- [ ] `RUNBOOK.md` documenta deploy inicial, update, rollback, monitoring.
- [ ] Suite tests sigue verde (~388+ tests con DocsRestrictionTest nuevo).

## Riesgos y notas

1. **Volumen `app_public` compartido entre app y nginx:** Compose v3 no soporta `volumes_from`. Solución: usar named volume + script init que copia desde la imagen al primer arranque. O cambiar el approach: nginx lee `public/` directamente del container app via shared volume mount.
2. **OPcache `validate_timestamps=0` requiere reload en cada deploy:** el deploy script debe ejecutar `php artisan opcache:clear` o reiniciar el container app después de migrar.
3. **CSP en `security-headers.conf`:** la actual permite `unsafe-inline` para scripts (Alpine + Leaflet). Endurecer en una iteración futura usando hashes/nonces.
4. **Traefik vs Caddy:** las labels son para Traefik. Si se usa Caddy, reemplazar con un `Caddyfile` (más simple, ACME automático sin labels).
5. **MariaDB sin port en prod:** la DB no expone puerto al host. Para administrar (mariadb-shell), `docker compose exec db mariadb -u root -p`.
6. **Backups sin off-site:** `backup.sh` deja todo en el host. Para resilencia real, hacer rsync a S3/B2 después: agregar al script un step opcional con `aws s3 sync` o `rclone`.
7. **CI artifacts:** el job `assets` sube `public/build` como artifact — útil para el release workflow consumirlos en el build de imagen sin re-compilar.
8. **Pulse + Sentry:** documentados en RUNBOOK como opcionales; instalar en una iteración futura cuando haya tráfico real que justifique observabilidad detallada.

Con todo esto verde, el proyecto está **production-ready**: build reproducible, deploy automatizado, rollback en un comando, backups + restore, monitoring básico y CI/CD.
