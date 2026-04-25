# Fase 6 — API privada + Mails transaccionales (Resend)

**Fuente:** spec §7 Routing + §10 Integraciones (ajustado: la API **NO es pública**, es privada con Sanctum).
**Prerequisitos:** Fase 5 mergeada (310 tests, sitio público funcionando).
**Meta:** API autenticada con Sanctum para consumo de apps internas/integraciones, más todos los mails transaccionales reales vía Resend (contacto, newsletter double opt-in, clasificados contact-to-owner, campañas).

## Principios

- **API privada.** Todo endpoint bajo `/api/v1/*` requiere `auth:sanctum` con Bearer token. No hay endpoints read-only abiertos.
- **Tokens por usuario admin.** Cada `User` puede emitir N tokens desde su propio perfil admin. Los tokens heredan los permisos Spatie del usuario.
- **JsonResource envelope `{ data, meta }`.** Consistente entre endpoints. Paginación en `meta`.
- **Rate limit por token.** Default 120 req/min. Configurable por rol.
- **Mails vía Resend con fallback `log`.** En dev el driver es `log` (mails al `storage/logs/laravel.log`). En prod con `RESEND_KEY` seteado, envía real.
- **Queue para mails.** Todo mail va al queue Redis (driver ya configurado). `SendCampaign` en chunks de 100.

---

## Task 1: Sanctum + user tokens management

**Archivos:**
- Install: `laravel/sanctum`
- Modify: `app/config/sanctum.php`
- Modify: `app/app/Models/User.php` — add `HasApiTokens` trait
- Create: `app/app/Http/Controllers/Admin/ApiTokenController.php`
- Create: `app/resources/views/admin/profile/tokens.blade.php`
- Modify: `app/routes/admin.php` — tokens management routes
- Modify: `app/bootstrap/app.php` — Sanctum middleware aliases
- Create: `app/tests/Feature/Admin/AdminApiTokensTest.php`

### Step 1: Install Sanctum

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker compose exec -T app composer require laravel/sanctum
docker compose exec -T app php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
docker compose exec -T app php artisan migrate --no-interaction
```

Verificar que se creó `2019_12_14_000001_create_personal_access_tokens_table` (Sanctum default).

### Step 2: User model

En `app/app/Models/User.php`:
```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;
    // ...
}
```

### Step 3: Token management UI en admin

Crear `app/app/Http/Controllers/Admin/ApiTokenController.php`:
```php
class ApiTokenController extends Controller
{
    public function index(): View
    {
        $tokens = auth()->user()->tokens()->latest()->get();
        return view('admin.profile.tokens', compact('tokens'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['name' => 'required|string|max:100']);
        $user = auth()->user();
        $token = $user->createToken($request->name, $user->getPermissionNames()->toArray());
        return redirect()->route('admin.tokens.index')
            ->with('new_token', $token->plainTextToken)
            ->with('success', 'Token creado. Copialo ahora, no se volverá a mostrar.');
    }

    public function destroy($id): RedirectResponse
    {
        auth()->user()->tokens()->where('id', $id)->delete();
        return redirect()->route('admin.tokens.index')->with('success', 'Token revocado.');
    }
}
```

### Step 4: Admin routes

En `app/routes/admin.php` dentro del grupo auth:
```php
Route::get('tokens', [ApiTokenController::class, 'index'])->name('tokens.index');
Route::post('tokens', [ApiTokenController::class, 'store'])->name('tokens.store');
Route::delete('tokens/{id}', [ApiTokenController::class, 'destroy'])->name('tokens.destroy');
```

### Step 5: View

Crear `app/resources/views/admin/profile/tokens.blade.php`:
- Form para crear token nuevo (solo name)
- Si hay `new_token` en session, mostrar en banner coral con botón "Copiar" (Alpine)
- Tabla con tokens existentes: name, last_used_at, created_at, botón "Revocar"
- Nota: los permisos del token = permisos del usuario al momento de crearlo (snapshot)

### Step 6: Test

Crear `app/tests/Feature/Admin/AdminApiTokensTest.php`:
- admin_can_create_token
- admin_sees_plaintext_token_once
- admin_can_revoke_token
- non_admin_users_can_also_manage_own_tokens (editor, moderator)

### Step 7: Commit

```bash
git add app/
git commit -m "feat(api): Sanctum + token management en admin profile"
```

---

## Task 2: API v1 foundation + base controller + routes

**Archivos:**
- Create: `app/app/Http/Controllers/Api/V1/Controller.php` (base abstract)
- Create: `app/app/Http/Resources/ApiResource.php` (base con envelope)
- Create: `app/routes/api.php` (ya existe en Laravel 11 bootstrap, verificar)
- Modify: `app/bootstrap/app.php` — agregar `withRouting(api: __DIR__.'/../routes/api.php', apiPrefix: 'api')` y middleware Sanctum
- Create: `app/app/Http/Middleware/EnsureTokenAbility.php` (optional helper)
- Create: `app/tests/Feature/Api/ApiFoundationTest.php`

### Step 1: Enable API routes en Laravel 11

En `app/bootstrap/app.php`:
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi(); // Habilita Sanctum SPA si hace falta
        // ... aliases existentes
    })
    // ...
```

### Step 2: Base Controller

Crear `app/app/Http/Controllers/Api/V1/Controller.php`:
```php
namespace App\Http\Controllers\Api\V1;

abstract class Controller extends \App\Http\Controllers\Controller
{
    protected function envelope($data, array $meta = []): array
    {
        if ($data instanceof \Illuminate\Pagination\AbstractPaginator) {
            return [
                'data' => $data->items(),
                'meta' => array_merge([
                    'total'        => $data->total(),
                    'per_page'     => $data->perPage(),
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                ], $meta),
            ];
        }
        return ['data' => $data, 'meta' => $meta];
    }
}
```

### Step 3: ApiResource base

Crear `app/app/Http/Resources/ApiResource.php`:
```php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

abstract class ApiResource extends JsonResource
{
    public function with($request): array
    {
        return [
            'meta' => [
                'version' => 'v1',
                'generated_at' => now()->toIso8601String(),
            ],
        ];
    }
}
```

### Step 4: routes/api.php

Crear (o modificar si existe) `app/routes/api.php`:
```php
<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->name('api.v1.')->group(function () {
    // Health + whoami
    Route::get('/me', function () {
        return response()->json([
            'data' => [
                'id' => auth()->id(),
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'roles' => auth()->user()->roles->pluck('name'),
                'abilities' => auth()->user()->currentAccessToken()->abilities ?? [],
            ],
        ]);
    })->name('me');

    // Endpoints se agregan en tasks siguientes
});
```

### Step 5: Test foundation

Crear `app/tests/Feature/Api/ApiFoundationTest.php`:
```php
public function test_unauthenticated_request_returns_401(): void
{
    $this->getJson('/api/v1/me')->assertUnauthorized();
}

public function test_authenticated_with_token_returns_user(): void
{
    $user = User::factory()->create()->assignRole('admin');
    $token = $user->createToken('test')->plainTextToken;

    $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonStructure(['data' => ['id', 'email', 'roles']]);
}

public function test_invalid_token_returns_401(): void
{
    $this->withHeader('Authorization', 'Bearer invalid-token-xyz')
        ->getJson('/api/v1/me')
        ->assertUnauthorized();
}
```

### Step 6: Commit

```bash
git add app/
git commit -m "feat(api): foundation v1 con Sanctum auth + envelope {data,meta}"
```

---

## Task 3: API read endpoints — content + directory + data

Endpoints GET read-only con JsonResource envelope. Organizados por módulo.

**Endpoints a implementar (todos GET, todos requieren `{module}.view` permission):**

### Content
- `GET /api/v1/news` — listado paginado
- `GET /api/v1/news/{slug}` — detalle
- `GET /api/v1/events` — listado
- `GET /api/v1/events/{slug}` — detalle
- `GET /api/v1/recipes` — listado
- `GET /api/v1/recipes/{slug}` — detalle
- `GET /api/v1/pages/{slug}` — detalle

### Directory
- `GET /api/v1/lodgings` — listado con filter `?type=`
- `GET /api/v1/lodgings/{slug}` — detalle
- `GET /api/v1/venues` — listado con filter `?category=`
- `GET /api/v1/venues/{slug}` — detalle
- `GET /api/v1/rentals` — listado
- `GET /api/v1/rentals/{slug}` — detalle
- `GET /api/v1/service-providers` — listado
- `GET /api/v1/nearby-places` — listado
- `GET /api/v1/useful-info` — listado completo (no paginado, solo ~6 rows)
- `GET /api/v1/classifieds` — listado
- `GET /api/v1/classifieds/{slug}` — detalle
- `GET /api/v1/gallery` — listado paginado

### Data
- `GET /api/v1/tides?date=YYYY-MM-DD` — tide del día (default hoy)
- `GET /api/v1/tides/week?date=YYYY-MM-DD` — semana del día
- `GET /api/v1/weather` — lee `cache('weather:current')`

### Implementación

Por cada módulo:
1. `app/Http/Controllers/Api/V1/{Module}Controller.php` con index + show que autorizan + pagineado
2. `app/Http/Resources/{Module}Resource.php` con toArray() mapeando campos

Ejemplo `NewsController`:
```php
class NewsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', News::class);
        $news = News::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with('category', 'media')
            ->when($request->filled('q'), fn($q) => $q->where('title', 'like', '%'.$request->q.'%'))
            ->latest('published_at')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return NewsResource::collection($news);
    }

    public function show(News $news)
    {
        $this->authorize('view', $news);
        return new NewsResource($news->load('category', 'media'));
    }
}
```

Ejemplo `NewsResource`:
```php
class NewsResource extends ApiResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'body' => $this->body,
            'excerpt' => $this->excerpt,
            'reading_minutes' => $this->reading_minutes,
            'views' => $this->views,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'media' => $this->whenLoaded('media', fn() =>
                $this->media->map(fn($m) => [
                    'url' => url('storage/' . $m->path),
                    'alt' => $m->alt,
                    'sort_order' => $m->sort_order,
                ])
            ),
            'links' => [
                'self' => route('api.v1.news.show', $this->slug),
            ],
        ];
    }
}
```

### Routes

En `routes/api.php` dentro del grupo `auth:sanctum / v1`:
```php
Route::get('news', [NewsController::class, 'index'])->name('news.index');
Route::get('news/{news:slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('events', [EventController::class, 'index'])->name('events.index');
Route::get('events/{event:slug}', [EventController::class, 'show'])->name('events.show');
Route::get('recipes', [RecipeController::class, 'index'])->name('recipes.index');
Route::get('recipes/{recipe:slug}', [RecipeController::class, 'show'])->name('recipes.show');
Route::get('pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');
Route::get('lodgings', [LodgingController::class, 'index'])->name('lodgings.index');
Route::get('lodgings/{lodging:slug}', [LodgingController::class, 'show'])->name('lodgings.show');
Route::get('venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('venues/{venue:slug}', [VenueController::class, 'show'])->name('venues.show');
Route::get('rentals', [RentalController::class, 'index'])->name('rentals.index');
Route::get('rentals/{rental:slug}', [RentalController::class, 'show'])->name('rentals.show');
Route::get('service-providers', [ServiceProviderController::class, 'index'])->name('service-providers.index');
Route::get('nearby-places', [NearbyPlaceController::class, 'index'])->name('nearby-places.index');
Route::get('useful-info', [UsefulInfoController::class, 'index'])->name('useful-info.index');
Route::get('classifieds', [ClassifiedController::class, 'index'])->name('classifieds.index');
Route::get('classifieds/{classified:slug}', [ClassifiedController::class, 'show'])->name('classifieds.show');
Route::get('gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('tides', [TideController::class, 'index'])->name('tides.index');
Route::get('tides/week', [TideController::class, 'week'])->name('tides.week');
Route::get('weather', [WeatherController::class, 'index'])->name('weather.index');
```

### Tests

Crear `app/tests/Feature/Api/ApiReadTest.php` con ~15 tests (uno por módulo mayor + casos de autorización):
- test_list_news_requires_auth
- test_list_news_returns_paginated_with_envelope
- test_show_news_returns_full_resource
- test_list_events_filters_by_upcoming
- test_lodgings_with_type_filter
- test_venues_with_category_filter
- test_tides_today
- test_weather_reads_cache
- test_404_for_unknown_slug
- test_moderator_cannot_list_non_moderable_modules (forbidden por policy)

### Commit

```bash
git add app/
git commit -m "feat(api): endpoints read v1 con auth:sanctum + JsonResource envelope"
```

---

## Task 4: API write endpoints — mutations para content + moderation

Endpoints mutation con permisos Spatie. Solo los módulos que se necesitan realmente para una app consumidora (ej. staff móvil creando novedades, moderando clasificados, gestionando mensajes de contacto).

### Endpoints write

**Content (news/events/pages/recipes):**
- `POST /api/v1/news` — create (requiere `news.create`)
- `PUT /api/v1/news/{slug}` — update (requiere `news.update`)
- `DELETE /api/v1/news/{slug}` — delete (requiere `news.delete`)
- Mismo pattern para events, pages, recipes

**Moderation:**
- `DELETE /api/v1/classifieds/{slug}` — soft delete
- `DELETE /api/v1/contact-messages/{id}`
- `PATCH /api/v1/contact-messages/{id}/mark-read`
- `DELETE /api/v1/newsletter-subscribers/{id}`

**Form submissions desde apps externas:**
- `POST /api/v1/contact` — crea ContactMessage (sin auth! endpoint público para forms)
- Nota: este endpoint es excepción — debe estar FUERA del grupo auth:sanctum o con auth opcional.

### Routes adicionales

```php
// Writes en el grupo auth:sanctum
Route::apiResource('news', NewsController::class)->except(['index', 'show']);
Route::apiResource('events', EventController::class)->except(['index', 'show']);
// ... etc

// Moderation
Route::patch('contact-messages/{message}/mark-read', [ContactMessageController::class, 'markRead']);
Route::delete('classifieds/{classified:slug}', [ClassifiedController::class, 'destroy']);

// Fuera del grupo — endpoint público para forms externos (ej. app móvil del balneario)
Route::post('/api/v1/contact', [PublicApiController::class, 'contact'])->middleware('throttle:10,1');
```

### FormRequests

Reutilizar los FormRequests del admin cuando sea posible. Si los rules difieren (ej. el API no recibe file uploads), crear `Api/V1/StoreNewsRequest.php` específico.

### Tests

- test_admin_creates_news_via_api
- test_editor_can_create_news
- test_moderator_cannot_create_news
- test_moderator_can_delete_classified
- test_contact_message_marked_read
- test_public_contact_submission_creates_record
- test_public_contact_throttle_rejects_after_10

### Commit

```bash
git add app/
git commit -m "feat(api): endpoints write v1 con policies + moderation + public contact"
```

---

## Task 5: Rate limiting + OpenAPI docs

### Rate limiting

En `app/bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    // ...
    $middleware->api(prepend: [
        \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ]);
    $middleware->throttleApi();
})
```

Personalizar en `app/app/Providers/AppServiceProvider.php` dentro de `boot()`:
```php
RateLimiter::for('api', function (Request $request) {
    $user = $request->user();
    if (!$user) return Limit::perMinute(30)->by($request->ip());

    if ($user->hasRole('admin')) return Limit::perMinute(300)->by($user->id);
    if ($user->hasRole('editor')) return Limit::perMinute(180)->by($user->id);
    return Limit::perMinute(120)->by($user->id);
});
```

### OpenAPI docs

Opciones:
- **Scribe** (laravel/scribe) — genera docs desde controllers + comentarios
- **Mano a mano** — escribir YAML/JSON OpenAPI 3.0

Recomiendo Scribe:
```bash
docker compose exec -T app composer require --dev knuckleswtf/scribe
docker compose exec -T app php artisan vendor:publish --tag=scribe-config
docker compose exec -T app php artisan scribe:generate
```

Servir en `/api/docs` en dev; NO incluir en prod (controlar con `APP_ENV`).

### Tests

- test_rate_limit_kicks_in_after_threshold
- test_different_roles_get_different_limits

### Commit

```bash
git add app/
git commit -m "feat(api): rate limiting por rol + docs OpenAPI via Scribe"
```

---

## Task 6: Resend + mailables transaccionales

### Setup Resend

```bash
docker compose exec -T app composer require resend/resend-laravel
docker compose exec -T app php artisan vendor:publish --tag=resend-config
```

En `app/.env`:
```
MAIL_MAILER=log                              # dev default
# En prod:
# MAIL_MAILER=resend
# RESEND_KEY=re_xxxxxxxxxxxxxxxxxxxx
MAIL_FROM_ADDRESS="no-reply@elcondor.gob.ar"
MAIL_FROM_NAME="Balneario El Cóndor"

ADMIN_NOTIFICATION_EMAIL=turismo@elcondor.gob.ar
```

En `app/config/mail.php` agregar mailer:
```php
'mailers' => [
    // ... existing
    'resend' => [
        'transport' => 'resend',
    ],
],
```

### Mailables

Crear los siguientes en `app/app/Mail/`:

1. **ContactMessageReceivedMail** — notifica al admin cuando llega form de contacto
2. **NewsletterConfirmMail** — envía link de confirmación al subscriber
3. **NewsletterWelcomeMail** — bienvenida después de confirmar
4. **NewsletterUnsubscribedMail** — confirma baja
5. **ClassifiedContactMail** — envía mensaje al owner del clasificado
6. **AdvertisingContactReceivedMail** — notifica al admin
7. **EventRegistrationConfirmationMail** — confirmación de inscripción al usuario

Cada Mailable con plantilla Blade en `app/resources/views/mail/{name}.blade.php` usando Laravel Markdown (`@component('mail::message')`).

### Integrar en controllers

**ContactController::store:**
```php
ContactMessage::create([...]);
Mail::to(config('mail.admin_address'))->queue(new ContactMessageReceivedMail($message));
```

**NewsletterController::subscribe:**
```php
$sub = NewsletterSubscriber::updateOrCreate([...]);
Mail::to($sub->email)->queue(new NewsletterConfirmMail($sub));
```

**NewsletterController::confirm:**
```php
$sub->update(['status' => 'confirmed', 'confirmed_at' => now()]);
Mail::to($sub->email)->queue(new NewsletterWelcomeMail($sub));
```

**ClassifiedController::storeContact:**
```php
$contact = ClassifiedContact::create([...]);
Mail::to($classified->contact_email)
    ->queue(new ClassifiedContactMail($classified, $contact));
```

**AdvertisingController::store:**
```php
$ad = AdvertisingContact::create([...]);
Mail::to(config('mail.admin_address'))->queue(new AdvertisingContactReceivedMail($ad));
```

**EventController::register:**
```php
$registration = EventRegistration::create([...]);
Mail::to($registration->email)
    ->queue(new EventRegistrationConfirmationMail($event, $registration));
```

### Queue

Los mails usan `->queue()` → van al queue Redis. El worker ya corre como servicio Docker (definido en Fase 1).

### Tests

Crear `app/tests/Feature/MailTest.php` usando `Mail::fake()`:
- test_contact_submission_queues_admin_notification
- test_newsletter_subscribe_queues_confirmation
- test_classified_contact_queues_mail_to_owner
- test_event_registration_queues_confirmation
- test_publicite_submission_queues_admin_notification

### Commit

```bash
git add app/
git commit -m "feat(mail): Resend setup + 7 mailables transaccionales encolados"
```

---

## Task 7: SendCampaign real + tracking

### Update SendCampaign job (stub creado en Fase 4 Task 7)

Reemplazar `app/app/Jobs/SendCampaign.php`:

```php
<?php

namespace App\Jobs;

use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1800; // 30 min max
    public int $tries = 3;

    public function __construct(public NewsletterCampaign $campaign) {}

    public function handle(): void
    {
        $this->campaign->update(['status' => 'sending']);
        $sent = 0;

        NewsletterSubscriber::where('status', 'confirmed')
            ->orderBy('id')
            ->chunk(100, function ($subs) use (&$sent) {
                foreach ($subs as $sub) {
                    try {
                        Mail::to($sub->email)->send(
                            new NewsletterCampaignMail($this->campaign, $sub)
                        );
                        $sent++;
                    } catch (\Throwable $e) {
                        Log::warning('Campaign send fail', [
                            'campaign' => $this->campaign->id,
                            'subscriber' => $sub->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                // Update progress parcial
                $this->campaign->update(['sent_count' => $sent]);
            });

        $this->campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $sent,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->campaign->update(['status' => 'failed']);
        Log::error('SendCampaign failed', ['id' => $this->campaign->id, 'error' => $exception->getMessage()]);
    }
}
```

### Mailable

Crear `app/app/Mail/NewsletterCampaignMail.php` + template `resources/views/mail/campaign.blade.php`. Cada email incluye:
- Subject = `$campaign->subject`
- HTML = `$campaign->body_html` (admin redactó)
- Variables reemplazadas: `{{name}}`, `{{email}}`, `{{unsubscribe_url}}` → `route('newsletter.unsubscribe', $sub->confirmation_token)`
- Footer con link unsubscribe obligatorio

### Migration: agregar 'failed' al enum status

`2026_04_25_000001_add_failed_to_newsletter_campaigns_status_enum.php`:
```php
DB::statement("ALTER TABLE newsletter_campaigns MODIFY COLUMN status ENUM('draft','sending','sent','failed') DEFAULT 'draft'");
```

### Tests

- test_send_campaign_sends_to_confirmed_only
- test_send_campaign_updates_sent_count
- test_send_campaign_marks_sent_when_done
- test_send_campaign_failure_marks_failed

Usar `Mail::fake()` + `Bus::fake()`.

### Commit

```bash
git add app/
git commit -m "feat(mail): SendCampaign real con chunks de 100 + progress tracking"
```

---

## Task 8: Tests E2E + no-regresión

1. **Correr suite completa** post todos los commits:
   ```
   docker compose exec -T app php artisan test
   ```
   Esperado: 310 (Fase 5) + ~30 (API) + ~10 (Mails) + ~5 (Campaign) = ~355 tests.

2. **Smoke HTTP API:**
   ```bash
   # Token de prueba
   TOKEN=$(docker compose exec -T app php artisan tinker --execute="
       echo \App\Models\User::factory()->create()->assignRole('admin')->createToken('smoke')->plainTextToken;
   ")

   curl -s -H "Authorization: Bearer $TOKEN" http://localhost:18081/api/v1/me | head
   curl -s -H "Authorization: Bearer $TOKEN" http://localhost:18081/api/v1/news?per_page=3 | jq '.meta'
   curl -s -o /dev/null -w "%{http_code}\n" http://localhost:18081/api/v1/me   # sin auth → 401
   ```

3. **Smoke mails (log driver):**
   ```
   docker compose exec -T app tail -50 storage/logs/laravel.log | grep -A5 "Mail"
   ```

4. **Queue worker:**
   ```
   docker compose logs worker --tail=30
   ```
   Verificar que el worker procesa los jobs encolados.

### Commit final

```bash
git add app/
git commit -m "feat(api+mail): tests E2E + smoke de token auth + mail en queue"
```

---

## Criterios de aceptación de Fase 6

- [ ] Todo endpoint `/api/v1/*` requiere token Sanctum (salvo `/api/v1/contact` público con throttle).
- [ ] Admin UI permite crear, listar y revocar tokens del propio usuario.
- [ ] Permisos Spatie respetados en endpoints (viewAny, create, update, delete).
- [ ] Envelope `{ data, meta }` consistente en todos los endpoints.
- [ ] Rate limiting por rol: admin 300 rpm, editor 180 rpm, moderator 120 rpm.
- [ ] 7 mailables transaccionales funcionando, encolados en Redis.
- [ ] SendCampaign procesa confirmed en chunks de 100, tracking `sent_count`.
- [ ] OpenAPI docs accesible en dev en `/api/docs` (oculto en prod).
- [ ] Tests: suite ~355+ tests verdes.
- [ ] Mail driver default: `log` (dev). En prod con `RESEND_KEY` seteado, envía real.

## Riesgos

1. **Sanctum stateful vs stateless:** si vamos full stateless (apps nativas), no hace falta `statefulApi()`. Solo poner si en el futuro hay SPA compartiendo cookies.
2. **Token permisos como snapshot:** si un usuario pierde un permiso, sus tokens existentes siguen con ese permiso hasta revocarse. Documentar en UI.
3. **Resend API key:** el usuario debe crear una cuenta en resend.com y pegar la key. En tests, `Mail::fake()`. En dev, `log` driver.
4. **Rate limit storage:** usa Redis por default, ya disponible.
5. **Queue sobrecargado con campaign:** un campaign a 1000 subscribers genera 1000 jobs. El worker default procesa secuencialmente. Si tarda, agregar más workers o aumentar concurrency.
6. **Endpoint `/api/v1/contact` público:** es una excepción intencional (para apps externas sin auth). Mitigaciones: throttle 10/min por IP, honeypot, validación estricta.
7. **Scribe en prod:** la doc revela estructura de API. Ocultar con `config('scribe.enabled')` = false en prod.

Con todo esto verde, la plataforma está completa. Próximo paso: **Plan 7 — Deploy prod + CI** (Dockerfile multi-stage prod, nginx prod config, GitHub Actions, monitoring, backups).
