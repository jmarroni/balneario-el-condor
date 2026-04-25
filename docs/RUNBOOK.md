# Runbook — Balneario El Cóndor

Operaciones de producción. Audiencia: ops/devs con acceso al VPS.

## Stack

- VPS Linux con Docker Engine 24+ y Docker Compose v2.
- Traefik (o Caddy) como reverse proxy con HTTPS automático (Let's Encrypt).
- Servicios via Docker Compose: `app`, `nginx`, `db` (mariadb 11), `redis` (7), `worker`, `scheduler`.
- Imagen `balneario:<version>` construida con target `prod` del Dockerfile multi-stage.

## Deploy inicial

### 1. Clonar el repo

```bash
git clone https://github.com/<owner>/balneario-el-condor.git /opt/balneario
cd /opt/balneario
```

### 2. Generar `.env.production`

```bash
cp app/.env.production.example app/.env.production
nano app/.env.production
```

Completar:
- `APP_KEY` ← generar con: `docker compose -f app/docker-compose.yml -f app/docker-compose.prod.yml run --rm app php artisan key:generate --show`
- `APP_URL`, `APP_DOMAIN`
- `DB_PASSWORD`, `MYSQL_ROOT_PASSWORD`, `MYSQL_PASSWORD` (todos secrets nuevos)
- `REDIS_PASSWORD`
- `RESEND_KEY` (desde dashboard.resend.com)
- `ADMIN_NOTIFICATION_EMAIL`
- (Opcional) `SENTRY_LARAVEL_DSN`

### 3. Levantar Traefik (si aplica)

Ver `docs/traefik-setup.md` (TODO crear si se usa). Si usás Caddy o reverse proxy ya configurado, ajustar las labels de `nginx` en `docker-compose.prod.yml`.

### 4. Build + arranque

```bash
cd /opt/balneario/app
docker compose -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.production build
docker compose -f docker-compose.yml -f docker-compose.prod.yml --env-file .env.production up -d
```

### 5. Migrar la DB

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan migrate --force
```

### 6. Seeder de roles + permisos

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan db:seed --class=RolesAndPermissionsSeeder --force
```

### 7. Crear primer usuario admin

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan tinker
```

Dentro del REPL:

```php
$user = \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@elcondor.gob.ar',
    'password' => bcrypt('CAMBIAR_ESTA_PASSWORD'),
]);
$user->assignRole('admin');
exit
```

### 8. ETL desde el legacy (una sola vez)

Solo si no se hizo previamente:

```bash
docker compose -f docker-compose.yml -f docker-compose.prod.yml --profile etl up -d legacy_db
# esperar 30-60s a que healthy
docker compose -f docker-compose.yml -f docker-compose.prod.yml exec app php artisan etl:all
docker compose -f docker-compose.yml -f docker-compose.prod.yml --profile etl down
```

### 9. Smoke

```bash
./ops/smoke.sh
```

Debe terminar con `✓ Todo verde`.

### 10. Configurar cron del host

```bash
crontab -e
# Pegar: 0 3 * * * /opt/balneario/ops/backup.sh >> /var/log/balneario-backup.log 2>&1
```

## Deploy de actualización

```bash
cd /opt/balneario
./ops/deploy.sh v1.2.3
```

El script hace, en orden:
1. Backup pre-deploy → `/var/backups/balneario/<timestamp>/`
2. `git fetch --tags && git checkout <version>`
3. `docker compose build app` con target `prod`
4. `docker compose run --rm app php artisan migrate --force`
5. `docker compose up -d --force-recreate app worker scheduler nginx`
6. Espera 10s + corre `smoke.sh`

Si el smoke falla, el container nuevo está corriendo pero queda el backup pre-deploy listo para rollback.

## Rollback

### Opción A — checkout a versión anterior

```bash
./ops/deploy.sh v1.2.2
```

### Opción B — restaurar backup completo

```bash
./ops/restore.sh /var/backups/balneario/<timestamp-pre-deploy>
```

`restore.sh` pide confirmación `yes`. Sobrescribe DB y storage del entorno actual.

## Backups

Diarios a las 3 AM (cron del host). Retención automática 14 días.

```bash
ls -lh /var/backups/balneario/
# Cada timestamp tiene db.sql.gz + storage.tar.gz
```

### Off-site (recomendado)

Sumar al cron un sync con rclone hacia S3/B2/GDrive después del backup local. Ver `ops/cron.example`.

## Logs

```bash
# App
docker compose logs app -f --tail 200

# Worker (queue)
docker compose logs worker -f --tail 200

# Scheduler (cron Laravel)
docker compose logs scheduler -f --tail 50

# Nginx
docker compose logs nginx -f --tail 100

# Storage logs (laravel.log)
docker compose exec app tail -f storage/logs/laravel.log
```

## Monitoring

### Healthcheck

```bash
curl https://elcondor.gob.ar/up
# → 200 OK
```

Ideal: monitorear desde un servicio externo (UptimeRobot, Healthchecks.io) cada 5 min.

### Pulse (opcional)

Para activar Laravel Pulse:

```bash
docker compose exec app composer require laravel/pulse
docker compose exec app php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"
docker compose exec app php artisan migrate
```

Dashboard en `/pulse` (proteger con role:admin).

### Sentry (opcional)

Setear `SENTRY_LARAVEL_DSN` en `.env.production` y reiniciar containers.

```bash
docker compose exec app composer require sentry/sentry-laravel
docker compose restart app worker scheduler
```

## Troubleshooting

### Mails no se envían

```bash
docker compose exec app php artisan queue:failed
docker compose exec app php artisan queue:retry all
```

Verificar `RESEND_KEY` válido. Si falla con 401, regenerar la key en dashboard.resend.com.

Para ver el último mail intentado:

```bash
docker compose exec app tail -100 storage/logs/laravel.log | grep -A5 -i "mail\|resend"
```

### DB lenta

```bash
docker compose exec app php artisan db:show
docker compose exec db mariadb -u root -p -e "SHOW PROCESSLIST;"
```

### Reset caches después de deploy con env nuevo

```bash
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan event:cache
```

(Estos caches ya se aplican automáticamente en el entrypoint cuando `APP_ENV=production`.)

### Imagen pesada

```bash
docker images | grep balneario
docker history balneario:<version>
```

Si supera 500 MB, revisar `app/.dockerignore` y stages del Dockerfile.

### Worker no procesa jobs

```bash
docker compose ps worker
docker compose restart worker
docker compose exec app php artisan queue:work --stop-when-empty
```

### Acceder a la DB desde el host

```bash
docker compose exec db mariadb -u root -p
# password de MYSQL_ROOT_PASSWORD
```

### Access /docs en producción

Por default deshabilitado. Para activar:

```bash
# En .env.production:
SCRIBE_DOCS_ENABLED=true
SCRIBE_DOCS_BASIC_AUTH_USER=docs
SCRIBE_DOCS_BASIC_AUTH_PASS=<random>
```

Reiniciar app: `docker compose restart app`. Acceder con basic auth.

## Versionado

- Tags Git: `v<MAJOR>.<MINOR>.<PATCH>` (semver).
- Cada tag dispara el workflow GitHub Actions `release.yml` que builda y pushea la imagen a `ghcr.io/<owner>/balneario-el-condor/app:<tag>`.

```bash
# Crear release
git tag v1.0.1
git push --tags
# Esperar a que GHCR publique la imagen
# Deploy en el servidor:
ssh prod 'cd /opt/balneario && ./ops/deploy.sh v1.0.1'
```

## Contacto / responsables

- Dev: TODO
- Hosting / DNS: TODO
- Responsable comunicacional (turismo): turismo@elcondor.gob.ar
