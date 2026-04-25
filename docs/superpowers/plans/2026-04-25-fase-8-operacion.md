# Fase 8 — Mejoras post-launch (observabilidad + security + resiliencia)

**Fuente:** experiencia operativa post-Fase 7. Ya está deployable; ahora agregamos las cosas que se sienten una semana después de tener tráfico real.
**Prerequisitos:** Fase 7 mergeada (391 tests + Dockerfile prod + CI/CD + backups + runbook).
**Meta:** observabilidad real (qué pasa en prod), security upgrades (2FA + audit log), backups que sobreviven al VPS, performance via CDN, y quality of life para ops.

## Principios

- **Cada mejora debe ser opcional.** Si no se setea la env var, no se activa — el sitio sigue andando igual.
- **Sin lock-in caro.** Sentry tiene plan free; Plausible self-hosted es opción; rclone es FOSS; Cloudflare CDN es free.
- **Respeto a privacidad.** Analytics privacy-first (Plausible), no Google Analytics.
- **Auditable.** Todo cambio admin queda registrado.

---

## Task 1: Observabilidad — Pulse + Sentry

**Goal:** dashboard de salud + tracking de errores remoto.

**Archivos:**
- Install: `laravel/pulse`, `sentry/sentry-laravel`
- Modify: `app/config/pulse.php` (post-publish)
- Modify: `app/bootstrap/providers.php` (autoload Pulse)
- Modify: `app/.env.production.example` — agregar `SENTRY_LARAVEL_DSN`, `SENTRY_TRACES_SAMPLE_RATE`
- Modify: `app/routes/admin.php` — gateado para `/pulse`
- Create: migración Pulse
- Modify: `app/app/Http/Middleware/Authorize.php` (Pulse gate)
- Create: `app/tests/Feature/Hardening/PulseAccessTest.php`

### Pulse setup

```bash
docker compose exec -T app composer require laravel/pulse
docker compose exec -T app php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider" --tag=pulse-config
docker compose exec -T app php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider" --tag=pulse-migrations
docker compose exec -T app php artisan migrate
```

Gate `/pulse` solo a admin: en `app/Providers/AppServiceProvider::boot()`:

```php
use Laravel\Pulse\Facades\Pulse;

Pulse::user(fn ($user) => [
    'name'   => $user->name,
    'extra'  => $user->email,
    'avatar' => null,
]);

Gate::define('viewPulse', fn ($user) => $user->hasRole('admin'));
```

Ruta: registrada por Pulse automáticamente en `/pulse` con auth + gate.

Agregar link al sidebar admin solo para `admin`.

### Sentry setup

```bash
docker compose exec -T app composer require sentry/sentry-laravel
docker compose exec -T app php artisan sentry:publish --dsn=
```

En `app/bootstrap/app.php`, agregar al `withExceptions`:

```php
use Sentry\Laravel\Integration;

->withExceptions(function (Exceptions $exceptions) {
    Integration::handles($exceptions);
})
```

Filtros en `app/config/sentry.php`:
```php
'send_default_pii' => false,
'traces_sample_rate' => env('SENTRY_TRACES_SAMPLE_RATE', 0.1),
'profiles_sample_rate' => 0.0,
'ignore_exceptions' => [
    \Illuminate\Validation\ValidationException::class,
    \Illuminate\Auth\AuthenticationException::class,
    \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
],
```

### Tests

- `test_pulse_dashboard_requires_admin_role`
- `test_editor_cannot_access_pulse`
- `test_sentry_does_not_send_in_test_env` (verifica que `SENTRY_LARAVEL_DSN=` está vacío)

### Commit

```bash
git add app/
git commit -m "feat(observ): Laravel Pulse en /pulse + Sentry stub con sampling 10%"
```

---

## Task 2: 2FA admin con Fortify

**Goal:** mitigar riesgo de credentials leak en cuentas admin/editor.

**Archivos:**
- Install: `laravel/fortify`
- Modify: `app/config/fortify.php`
- Modify: `app/app/Models/User.php` — `TwoFactorAuthenticatable` trait
- Migración: `add_two_factor_columns_to_users_table` (la trae Fortify)
- Create: `app/resources/views/admin/profile/two-factor.blade.php`
- Modify: `app/routes/admin.php` — links a `/user/two-factor-*`
- Create: `app/tests/Feature/Admin/TwoFactorTest.php`

### Setup

```bash
docker compose exec -T app composer require laravel/fortify
docker compose exec -T app php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"
docker compose exec -T app php artisan migrate
```

Habilitar features en `config/fortify.php`:

```php
'features' => [
    Features::twoFactorAuthentication([
        'confirm' => true,
        'confirmPassword' => true,
    ]),
],
```

Coexistencia con Breeze: deshabilitar `Features::registration()` y `Features::resetPasswords()` (Breeze ya las maneja).

### UI

Card en `/admin/profile/two-factor`:
- Estado actual (activado/desactivado)
- Botón "Activar 2FA" → muestra QR (Google Authenticator/Authy)
- Después de activar, mostrar 8 recovery codes
- Botón "Regenerar recovery codes"
- Botón "Desactivar"

### Política

- 2FA opcional para `editor` y `moderator`.
- 2FA **obligatorio para `admin`** después de Plan 8 deploy. Implementar como middleware que redirige al setup si admin sin 2FA.

### Tests

- `test_user_can_enable_two_factor`
- `test_user_can_disable_two_factor`
- `test_recovery_codes_are_generated_and_unique`
- `test_admin_without_2fa_redirected_to_setup`

### Commit

```bash
git commit -m "feat(security): Fortify 2FA opcional para editor/moderator, obligatorio para admin"
```

---

## Task 3: Audit log de cambios admin (spatie/laravel-activitylog)

**Goal:** quién hizo qué cambio, cuándo, qué cambió.

**Archivos:**
- Install: `spatie/laravel-activitylog`
- Migración Spatie
- Modify: `app/app/Models/{News,Event,Lodging,Venue,Rental,Classified,Recipe,Page,User}.php` — `LogsActivity` trait + `getActivitylogOptions()`
- Create: `app/app/Http/Controllers/Admin/AuditLogController.php`
- Create: `app/resources/views/admin/audit-log/index.blade.php`
- Modify: `app/routes/admin.php` — `/admin/audit-log` con role:admin
- Create: `app/tests/Feature/Admin/AdminAuditLogTest.php`

### Setup

```bash
docker compose exec -T app composer require spatie/laravel-activitylog
docker compose exec -T app php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
docker compose exec -T app php artisan migrate
```

### Models loggeables

En cada model relevante:

```php
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class News extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'body', 'published_at', 'news_category_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => "creó la noticia :subject.title",
                'updated' => "actualizó la noticia :subject.title",
                'deleted' => "eliminó la noticia :subject.title",
                default => $eventName,
            });
    }
}
```

Aplicar el mismo pattern a Event, Lodging, Venue, Rental, Classified, Recipe, Page, User (audit del cambio de role).

### Admin UI

`/admin/audit-log`:
- Tabla con: Fecha, Usuario, Acción, Modelo, Cambios (JSON pretty)
- Filtros: rango de fecha, usuario, modelo
- Solo accessible por `admin`
- Exportar CSV button

### Tests

- `test_creating_news_logs_activity`
- `test_updating_news_logs_changes_only`
- `test_admin_sees_audit_log`
- `test_editor_cannot_see_audit_log`

### Commit

```bash
git commit -m "feat(security): audit log de cambios en 9 modelos via spatie/activitylog"
```

---

## Task 4: Off-site backups con rclone

**Goal:** sobrevivir a un VPS muerto.

**Archivos:**
- Modify: `ops/backup.sh` — agregar step rclone opcional
- Create: `ops/rclone.conf.example`
- Create: `ops/test-restore.sh` — verifica integridad del último backup
- Modify: `ops/cron.example` — agregar línea rclone

### Cambios a backup.sh

Agregar al final, antes del cleanup:

```bash
# Off-site sync (opcional, solo si RCLONE_REMOTE está set)
if [ -n "${RCLONE_REMOTE:-}" ]; then
    echo "→ Off-site sync → $RCLONE_REMOTE"
    rclone copy "$BACKUP_PATH" "$RCLONE_REMOTE/$TIMESTAMP" \
        --transfers 4 --checkers 8 --quiet
    echo "✓ Off-site OK"
fi
```

### .env.production.example

Agregar:
```
# Off-site backup (opcional)
RCLONE_REMOTE=                   # ej: s3:balneario-backups o b2:balneario-backups
```

### rclone.conf.example

```ini
# Configurar con: rclone config
# Backblaze B2 (recomendado: barato + S3-compatible)
[b2]
type = b2
account = APPLICATION_KEY_ID
key = APPLICATION_KEY

# AWS S3
[s3]
type = s3
provider = AWS
region = sa-east-1
access_key_id = AKIA...
secret_access_key = ...

# Cifrado encima (recomendado)
[balneario-encrypted]
type = crypt
remote = b2:balneario-raw-bucket
password = OBSCURED_PASSWORD
password2 = OBSCURED_SALT
```

### test-restore.sh

Script semanal que descarga el último backup off-site, lo monta en un container temporal, corre `mariadb` con ese dump, verifica que tiene >X tables y >Y rows en `news`. Si falla, manda alerta.

```bash
#!/usr/bin/env bash
# Verificación semanal: el último backup off-site se restaura OK?
set -euo pipefail

LATEST=$(rclone lsf "$RCLONE_REMOTE" | sort | tail -1)
TMPDIR=$(mktemp -d)
trap "rm -rf $TMPDIR" EXIT

rclone copy "$RCLONE_REMOTE/$LATEST/db.sql.gz" "$TMPDIR/"

# Container mariadb temporal
docker run -d --name backup-test --rm \
    -e MYSQL_ROOT_PASSWORD=test \
    -e MYSQL_DATABASE=balneario_test \
    mariadb:11

sleep 30

gunzip -c "$TMPDIR/db.sql.gz" | docker exec -i backup-test \
    mariadb -u root -ptest balneario_test

NEWS_COUNT=$(docker exec backup-test mariadb -u root -ptest -B -N \
    -e "SELECT COUNT(*) FROM balneario_test.news" 2>/dev/null || echo 0)

docker stop backup-test

if [ "$NEWS_COUNT" -lt 10 ]; then
    echo "✗ Backup test FAIL: solo $NEWS_COUNT news en el dump" >&2
    # TODO: notificar via webhook
    exit 1
fi
echo "✓ Backup verificado: $NEWS_COUNT news rows"
```

### Cron

Agregar a `cron.example`:
```cron
# Test restore semanal (domingos 04:00)
0 4 * * 0 /opt/balneario/ops/test-restore.sh >> /var/log/balneario-restore-test.log 2>&1
```

### Commit

```bash
git commit -m "feat(ops): off-site backup con rclone + test de restore semanal"
```

---

## Task 5: Cloudflare integration

**Goal:** CDN gratis + DDoS mitigation + cache de assets.

**Archivos:**
- Modify: `app/docker/nginx/prod.conf` — confiar en `CF-Connecting-IP`
- Create: `app/app/Http/Middleware/TrustCloudflareProxies.php` (extiende TrustProxies)
- Modify: `app/bootstrap/app.php` — agregar middleware Cloudflare en stack web
- Create: `docs/CLOUDFLARE.md` — setup con DNS + page rules + SSL mode

### TrustCloudflareProxies

Cloudflare manda el IP real del cliente en el header `CF-Connecting-IP`. Sin esto, Laravel ve siempre la IP del proxy CF.

```php
namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustCloudflareProxies extends Middleware
{
    /**
     * Cloudflare IPs publicadas en https://www.cloudflare.com/ips
     * Actualizar periódicamente o usar env var con lista cacheada.
     */
    protected $proxies = '*';  // Confiar en cualquier upstream — Cloudflare valida con CF-Visitor

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWSELB;
}
```

Mejor: una env `TRUSTED_PROXIES` con IPs explícitas de Cloudflare.

### nginx prod.conf

Agregar al server block:

```nginx
# Cloudflare real IP
real_ip_header CF-Connecting-IP;
real_ip_recursive on;

# Lista oficial CF (actualizar periódicamente)
set_real_ip_from 173.245.48.0/20;
set_real_ip_from 103.21.244.0/22;
set_real_ip_from 103.22.200.0/22;
set_real_ip_from 103.31.4.0/22;
set_real_ip_from 141.101.64.0/18;
set_real_ip_from 108.162.192.0/18;
set_real_ip_from 190.93.240.0/20;
set_real_ip_from 188.114.96.0/20;
set_real_ip_from 197.234.240.0/22;
set_real_ip_from 198.41.128.0/17;
set_real_ip_from 162.158.0.0/15;
set_real_ip_from 104.16.0.0/13;
set_real_ip_from 104.24.0.0/14;
set_real_ip_from 172.64.0.0/13;
set_real_ip_from 131.0.72.0/22;
```

### Headers Cache-Control para CDN

Verificar que los assets críticos tengan headers correctos:
- `/build/*` → `Cache-Control: public, max-age=31536000, immutable` ✓ (ya está)
- `/storage/*` → `Cache-Control: public, max-age=2592000` ✓ (ya está)
- `/img/*` → similar
- HTML → `Cache-Control: no-cache, must-revalidate` (default Laravel)

### docs/CLOUDFLARE.md

Doc paso a paso:
1. Crear cuenta Cloudflare gratis
2. Cambiar nameservers del dominio
3. SSL/TLS → "Full (strict)" + Always Use HTTPS
4. Caching → Browser TTL = 4 hours, Edge TTL = 30 days
5. Page Rules:
   - `*.elcondor.gob.ar/build/*` → Cache Everything, Edge TTL 1 month
   - `*.elcondor.gob.ar/admin/*` → Bypass Cache
   - `*.elcondor.gob.ar/api/*` → Bypass Cache
6. Security level → Medium
7. Bot Fight Mode → On

### Tests

Sin tests automatizados (depende de DNS prod). Solo smoke manual post-deploy.

### Commit

```bash
git commit -m "feat(perf): Cloudflare integration con TrustProxies + real_ip + Cache-Control"
```

---

## Task 6: Plausible Analytics

**Goal:** saber qué páginas son más visitadas, sin Google Analytics.

**Archivos:**
- Modify: `app/resources/views/components/public/layouts/main.blade.php` — script Plausible condicional
- Modify: `app/.env.production.example` — `PLAUSIBLE_DOMAIN`, `PLAUSIBLE_HOST` (opcional para self-hosted)

### Setup

Plausible cloud (free trial, ~$9/mes) o self-hosted en otro VPS.

En el layout, antes del `</head>`:

```blade
@if(config('app.plausible_domain'))
    <script defer
            data-domain="{{ config('app.plausible_domain') }}"
            @if(config('app.plausible_host')) src="{{ config('app.plausible_host') }}/js/script.js"
            @else src="https://plausible.io/js/script.js"
            @endif></script>
@endif
```

En `config/app.php`:
```php
'plausible_domain' => env('PLAUSIBLE_DOMAIN'),
'plausible_host'   => env('PLAUSIBLE_HOST'), // null = plausible.io cloud
```

### Tracking custom

Para eventos importantes (newsletter signup, contact form submit):

```blade
@push('scripts')
<script>
window.plausible = window.plausible || function() { (window.plausible.q = window.plausible.q || []).push(arguments) };
document.querySelector('form#newsletter')?.addEventListener('submit', () => {
    plausible('newsletter_signup');
});
</script>
@endpush
```

### Privacy

Plausible no usa cookies, no trackea PII, GDPR-compliant out of the box. No requiere banner de cookies.

### Commit

```bash
git commit -m "feat(analytics): Plausible privacy-first con eventos custom (newsletter, contact)"
```

---

## Task 7: Maintenance mode + deploy notifications

**Goal:** ops UX — saber cuándo se deployea + poder bajar el sitio sin caos.

### Maintenance mode

Laravel ya trae `php artisan down --secret="..."`. El secret permite bypass via `?secret=...`.

Agregar a `ops/deploy.sh` antes del migrate:

```bash
echo "→ Activando maintenance mode (con bypass secret)"
SECRET=$(openssl rand -hex 16)
$COMPOSE exec -T app php artisan down --render="errors::503" --secret="$SECRET"
echo "   Bypass: ${URL}?secret=${SECRET}"
```

Y al final, después del smoke OK:

```bash
echo "→ Desactivando maintenance mode"
$COMPOSE exec -T app php artisan up
```

Si el smoke falla, el sitio queda en maintenance — útil para investigar sin tráfico real entrando.

### Custom maintenance view

Crear `app/resources/views/errors/503.blade.php` con el mismo branding del sitio:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mantenimiento — Balneario El Cóndor</title>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,300..900&display=swap" rel="stylesheet">
    <style>/* mismos colores del sitio */</style>
</head>
<body>
    <div class="container">
        <img src="/img/logo.png" alt="">
        <h1>Volvemos en un rato.</h1>
        <p>Estamos haciendo mejoras al sitio. Si necesitás algo urgente:</p>
        <p><strong>Turismo Municipal: +54 9 2920 15 3300</strong></p>
    </div>
</body>
</html>
```

### Deploy notifications via Slack/Discord webhook

Agregar a `ops/deploy.sh`:

```bash
notify() {
    if [ -n "${DEPLOY_WEBHOOK_URL:-}" ]; then
        curl -sX POST -H "Content-Type: application/json" \
            -d "{\"text\": \"$1\"}" \
            "$DEPLOY_WEBHOOK_URL" > /dev/null
    fi
}

# Al inicio
notify "🚀 Deploy iniciado: $VERSION (rev $(git rev-parse --short HEAD))"

# Al final exitoso
notify "✅ Deploy $VERSION OK ($(date +%H:%M))"

# En el trap de error
trap 'notify "❌ Deploy $VERSION FAILED en $(basename $0)"' ERR
```

Configurar en `.env` o env del CI: `DEPLOY_WEBHOOK_URL` (Slack incoming webhook o Discord webhook).

### Commit

```bash
git commit -m "feat(ops): maintenance mode con secret bypass + custom 503 view + deploy webhooks"
```

---

## Task 8: CONTRIBUTING.md + ADRs + composer audit en CI

**Goal:** que un dev nuevo pueda contribuir sin romper nada + decisiones documentadas.

**Archivos:**
- Create: `CONTRIBUTING.md`
- Create: `docs/ADR/0001-laravel-11-stack.md`
- Create: `docs/ADR/0002-editorial-design-direction.md`
- Create: `docs/ADR/0003-sanctum-private-api.md`
- Create: `docs/ADR/README.md` — índice
- Modify: `.github/workflows/ci.yml` — agregar `composer audit` job

### CONTRIBUTING.md

Cubrir:
- Setup local (clone, docker compose up, migrate, seed)
- Convenciones de código (Pint, naming, structure)
- Workflow de Git (feature branches, conventional commits, PR template)
- Testing (cuando agregar tests, qué hacer si fallan)
- Plans + tasks (referenciar `docs/superpowers/plans/`)
- Code review checklist

### ADRs (Architecture Decision Records)

Pequeños docs sobre decisiones grandes:

**ADR-0001 — Laravel 11 stack:**
- Contexto: legado Zend 1.11, sitio caído
- Decisión: rewrite a Laravel 11 + Blade + Alpine + Tailwind
- Alternativas consideradas: Symfony, Django, Rails, Astro
- Consecuencias: deuda menor, comunidad activa, ecosistema PHP

**ADR-0002 — Diseño editorial costero:**
- Contexto: necesidad de identidad propia, no template
- Decisión: Fraunces + Instrument Sans + paleta sand/ink/sun/coral
- Alternativas: Bootstrap default, Tailwind UI templates
- Consecuencias: marca distintiva, más trabajo upfront

**ADR-0003 — API privada con Sanctum:**
- Contexto: necesidad de API para apps internas, no exponer al público
- Decisión: Sanctum + token bearer + policies Spatie
- Alternativas: Passport (OAuth), API keys estáticas
- Consecuencias: ergonomía simple, no OAuth flows complejos, granularidad de permisos

### CI: composer audit

Agregar a `ci.yml`:

```yaml
  audit:
    name: Composer audit
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: app
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - run: composer install --no-dev --no-scripts
      - run: composer audit --abandoned=fail --locked
```

Falla el build si hay vulnerabilidades conocidas en dependencias.

### npm audit

Para frontend:

```yaml
      - run: npm ci
      - run: npm audit --audit-level=high
```

### Commit

```bash
git commit -m "docs: CONTRIBUTING + 3 ADRs iniciales + CI composer/npm audit"
```

---

## Criterios de aceptación de Fase 8

- [ ] `/pulse` accesible solo a admin, muestra métricas reales (slow queries, exceptions, cache hit rate).
- [ ] Sentry captura excepciones no manejadas si `SENTRY_LARAVEL_DSN` está seteado.
- [ ] 2FA opcional para editor/moderator, obligatorio para admin (middleware redirige al setup).
- [ ] Cambios en News/Event/Lodging/etc generan rows en `activity_log` con who/when/diff.
- [ ] `/admin/audit-log` muestra los cambios filtrados.
- [ ] `ops/backup.sh` con `RCLONE_REMOTE` set sube backups a B2/S3 cifrados.
- [ ] `ops/test-restore.sh` corre semanal y verifica integridad.
- [ ] Cloudflare delante con `CF-Connecting-IP` propagada a Laravel via `real_ip_header`.
- [ ] Plausible muestra páginas + eventos custom (newsletter signup).
- [ ] `ops/deploy.sh` activa maintenance mode + manda webhook a Slack/Discord.
- [ ] CI corre `composer audit` y `npm audit` y rompe si hay HIGH/CRITICAL.
- [ ] CONTRIBUTING.md + 3 ADRs commiteados.
- [ ] Suite tests verde (~395-400+ tests con tests de Pulse access, 2FA, audit log).

## Riesgos

1. **Pulse storage:** los datos van a la DB principal. En sitios con tráfico alto, considerar `PULSE_DB_CONNECTION=pulse` separado.
2. **Sentry quota:** plan free son 5k events/mes — si una excepción se loopea, se quema rápido. Usar `before_send` en `sentry.php` para sampling agresivo de errores recurrentes.
3. **2FA secrets:** los recovery codes deben mostrarse UNA SOLA VEZ. Si el user los pierde, admin debe regenerarlos manualmente.
4. **Activity log volumen:** ~10 modelos × N cambios = mucha data. Configurar truncate >90 días en cron.
5. **Cloudflare cache invalidation:** después de un deploy, los assets `/build/*` cambian de hash automáticamente (Vite manifest), pero las page rules siguen sirviendo HTML viejo si TTL alto. Usar Cloudflare API para purge selectivo en deploy.
6. **Plausible costo cloud:** $9/mes el plan más chico. Self-hosted requiere VPS extra. Alternativa free: GoatCounter o Umami self-hosted.
7. **Webhook secrets:** `DEPLOY_WEBHOOK_URL` no debe ir al repo, solo en env del servidor que ejecuta el deploy.

## Tareas opcionales no incluidas (Plan 9?)

- **Image optimization automática:** Spatie Media Library v11 con responsive images.
- **Search:** Meilisearch o Typesense (Algolia es caro). Útil cuando haya >500 novedades.
- **Multi-tenant:** si querés clonar el sitio para otros pueblos costeros, separación por subdomain.
- **Mobile app PWA:** convertir el sitio en PWA con Service Worker + manifest.
- **A/B testing:** Laravel Pennant con feature flags por segmento.
- **Real-time:** Laravel Reverb para WebSockets (notificaciones admin en vivo).

Con Fase 8 verde, el proyecto está listo para operación a largo plazo: observable, auditable, resiliente, performant, y con un onboarding documentado para nuevos contributors.
