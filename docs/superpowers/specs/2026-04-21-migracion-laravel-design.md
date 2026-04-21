# Migración Balneario El Cóndor: Zend Framework 1 → Laravel 11

**Fecha:** 2026-04-21
**Autor:** Juan Pablo Marroni (brainstorming con Claude)
**Estado:** Diseño aprobado por stakeholder — pendiente de plan de implementación

---

## 1. Contexto y objetivo

Sitio de turismo de Balneario El Cóndor (Río Negro, Argentina). Stack actual:
Zend Framework 1 sobre PHP legacy, MariaDB y nginx, con dos aplicaciones Zend
paralelas (sitio público en `htdocs/` y área de administración en `socios/`).
El sitio hoy **no está en producción** (roto).

**Objetivo:** reescribir 1:1 funcional en Laravel 11 (última estable).
Diseño visual se tercerizará a Claude Design una vez migrado el stack;
esta etapa mantiene las vistas con Tailwind vanilla y layout mínimo.

**Alcance:** migración completa — sitio público + panel de administración
(reemplaza `socios/`), con DB nueva (schema Laravel-idiomático) alimentada
por ETL desde el dump legacy. Un solo proyecto Laravel, stack Docker
self-hosted en VPS propio.

**Fuera de alcance:** rediseño visual, SEO preservation de URLs legacy,
integración MercadoPago (dada de baja), cámara RTSP (dada de baja),
Club de Amigos (dado de baja).

---

## 2. Decisiones de producto (todas confirmadas con el stakeholder)

| Decisión | Elección |
|---|---|
| Alcance | Reescritura 1:1 funcional (diseño después) |
| Arquitectura | Laravel monolítico con Blade + rutas API bajo `/api/v1` |
| Apps incluidas | Sitio público + admin (ex-`socios/`) en un solo proyecto |
| DB | Schema nuevo Laravel-idiomático + ETL desde legacy |
| URLs | Nuevas, coherentes con Laravel (no hay SEO legacy que preservar) |
| Usuarios | Solo admins con roles diferenciados (`spatie/laravel-permission`) |
| Storage | Disco local con volumen Docker persistente |
| Hosting | VPS self-hosted con docker-compose completo |
| Entrega | Big-bang cutover (sin presión de downtime) |

**Módulos que se migran:** novedades, eventos (agenda + tejofiesta + primavera
unificados), mareas, hospedaje, gourmet + nocturnos (unificados en "venues"),
alquiler, clasificados (con imágenes y mail de contacto), servicios (directorio
de profesionales), recetas, galería pública de fotos, lugares cercanos,
información útil, páginas estáticas (historia, fauna, lugares, etc. como CRUD
editable), contacto, encuesta, newsletter (con service moderno), clima
(fuente externa moderna), publicite, formularios de contacto a clasificados.

**Módulos que se dan de baja:** MercadoPago (`clientes`), cámara VLC ActiveX,
Club de Amigos (`clubdeamigos`), agregador de feeds RSS.

**Defaults asumidos (flag para revisión):**
- Newsletter con sistema de campañas integrado desde el admin (no solo captura).
- Mareas con CRUD manual + importador CSV anual.
- Mapas con Leaflet + OpenStreetMap (no Google Maps).

---

## 3. Arquitectura de alto nivel

Un solo proyecto Laravel 11 sirviendo HTML por Blade y endpoints JSON bajo
`/api/v1`. Stack Docker de 6 contenedores activos + 1 efímero para ETL.

```
VPS (docker-compose)
├── nginx (80/443)         reverse proxy, sirve assets estáticos directo
├── app (php-fpm)          Laravel (web + API)
├── worker                 php artisan queue:work (mails, ETL, jobs)
├── scheduler              php artisan schedule:work (clima, mareas)
├── db (MariaDB 11)        schema nuevo
├── redis                  cache + queue + sessions
└── legacy_db (profile ETL, efímero)   restaurada desde balneario-el-condor.sql
```

**Stack de frontend (sin diseño final hasta Fase 9):**
- **Blade** para server-side rendering.
- **Tailwind CSS** como base (sin estilos propios hasta rediseño).
- **Alpine.js** para interactividad liviana (toggles, modales, dropdowns).
- **Vite** para bundling (default Laravel 11).
- **Leaflet + OpenStreetMap** para mapas.

**No usamos:** Livewire, Inertia, Vue/React, Jetstream. Blade + Alpine cubre
el 100% del scope (sitio informativo + admin con formularios).

**Stack de backend:**
- Laravel 11.x (PHP 8.3 FPM Alpine).
- MariaDB 11 (compatibilidad directa con dump legacy para ETL).
- Redis 7 (cache, queue, sessions).
- Laravel Breeze (auth scaffolding, sin registro público).
- `spatie/laravel-permission` (roles + permisos).
- `resend/resend-laravel` (mail transaccional + newsletter).

---

## 4. Organización del código Laravel

Feature folders: cada dominio agrupa su modelo + servicio + policy.
Los controllers viven aparte por capa de transporte (Public / Admin / Api).

```
app/
├── Domain/                   lógica de negocio por módulo
│   ├── News/                 (ex-novedades)
│   ├── Events/               (ex-agenda + tejofiesta + primavera)
│   ├── Lodgings/             (ex-hospedaje)
│   ├── Venues/               (ex-gourmet + nocturnos unificados)
│   ├── Rentals/              (ex-alquiler)
│   ├── Classifieds/
│   ├── ServiceProviders/     (ex-servicios)
│   ├── Recipes/
│   ├── Tides/                (ex-mareas)
│   ├── Weather/              (integración Open-Meteo)
│   ├── Gallery/              (ex-imagenes, galería pública)
│   ├── NearbyPlaces/         (ex-cercanos)
│   ├── UsefulInfo/           (ex-informacionutil)
│   ├── Pages/                (CRUD genérico: historia, fauna, etc.)
│   ├── Surveys/
│   ├── Newsletter/           (subscribers + campaigns)
│   ├── Contact/              (formulario contacto)
│   ├── Advertising/          (formulario publicite)
│   └── Users/                (admins + roles)
│
├── Http/
│   ├── Controllers/
│   │   ├── Public/           Blade del sitio público
│   │   ├── Admin/            Blade del panel /admin
│   │   └── Api/V1/           JSON versionado
│   ├── Requests/             FormRequest por acción
│   └── Middleware/
│
├── Providers/
└── Console/Commands/         comandos artisan (ETL, sync clima)

resources/views/
├── public/
└── admin/

routes/
├── web.php                   públicas
├── admin.php                 /admin con middleware auth + role
├── api.php                   /api/v1
└── console.php
```

**Unificaciones confirmadas:**
- `agenda` + `tejofiesta` + `primavera` → **`events`** + `event_registrations`
- `gourmet` + `nocturnos` → **`venues`** con columna `category`
- `historia`, `fauna`, `lugares`, `servicios-info`, `ubicacion`, etc. → **`pages`** (CRUD genérico con slug)

---

## 5. Schema de base de datos

### Convenciones nuevas

- Tablas en **inglés plural** (`news`, `events`, `lodgings`).
- Columnas **sin prefijo** (`title` en vez de `nov_titulo`).
- Timestamps estándar: `created_at`, `updated_at`, `deleted_at` (soft deletes).
- Charset uniforme: **`utf8mb4` + `utf8mb4_unicode_ci`**.
- IDs `bigint unsigned`, FKs reales con `ON DELETE` explícito.
- Fechas como `date`/`datetime` (el legacy tiene fechas en `varchar`).
- Una tabla **`media`** polimórfica para imágenes (reemplaza columnas `imagen0..imagen4` y tablas `*_imagenes`).
- Cada tabla migrada tiene una columna `legacy_id` (nullable) para trazabilidad e idempotencia del ETL.

### Mapeo legacy → nuevo

#### Tablas que se migran

| Legacy | Nuevo | Notas |
|---|---|---|
| `novedades` | `news` | `nov_titulo`→`title`, `nov_imagenes`→ tabla `media`, `nov_fechahora`→`published_at`, `nov_keyword`→`slug`, `nov_visitas`→`views`, `cn_id`→`news_category_id` |
| `categorias_novedades` | `news_categories` | `link` se descarta |
| `agenda` | `events` | `ag_fecha` varchar→`starts_at datetime` parseado, `ag_url_amigable`→`slug`, `ag_destacado`→`featured`, `ag_todo_dias`→`all_day`, `ag_orden`→`sort_order` |
| `tejofiesta` | `event_registrations` | Vinculadas a evento seed "Fiesta del Tejo" |
| `primavera` | `event_registrations` | Vinculadas a evento seed "Fiesta de la Primavera" |
| `hospedaje` | `lodgings` | `ho_tipo` (1=Hotel, 2=Casa, 3=Camping) → `type` enum; imágenes al polimórfico |
| `hospedaje_imagenes` | `media` | polimórfico, `mediable_type=Lodging` |
| `gourmet` + `nocturnos` | `venues` | Unificados con columna `category` (`gourmet` o `nightlife`) |
| `gourmet_imagenes` | `media` | polimórfico, `mediable_type=Venue` |
| `clasificados` | `classifieds` | `cla_fechahora`→`created_at`, `cla_categoria` varchar → FK `classified_category_id`, `cla_keyword`→`slug` |
| `clasificado_imagenes` | `media` | polimórfico; explotar `imagen0..4` a filas separadas |
| `clasificados_mail` | `classified_contacts` | `cla_ip_envio`→`ip_address`, `cla_fecha_envio`→`created_at` |
| `recetas` | `recipes` | `re_tiempo_preparacion`→`prep_minutes`, `re_tiempo_coccion`→`cook_minutes`, `re_ingredientes`→`ingredients` (markdown), `re_keyword`→`slug` |
| `mareas` | `tides` | Corregir typo `ma_segunda_plamar`→`second_high`; normalizar 4 columnas de hora + 4 de altura |
| `alquiler` | `rentals` | Explotar `uim_imagen0..4` a `media` |
| `servicios` | `service_providers` | Directorio de profesionales locales; `ser_nombre_contacto`→`contact_name`, `ser_mail_contacto`→`contact_email` |
| `informacionutil` | `useful_info` | Ya bastante sano; solo renombrar columnas |
| `encuesta` | `survey_responses` | `en_id_grupo`→`survey_id` (FK a nueva `surveys`) |
| `newsletter` | `newsletter_subscribers` | Validar mails al migrar; `nw_fecha` varchar→`subscribed_at` parseado |
| `usuarios` | `users` + `model_has_roles` | SHA1 no se puede rehashear a bcrypt → forzar reset de password en primer login; `us_rol` varchar → rol Spatie |
| `imagenes` | `gallery_images` | Galería pública del balneario, ~1955 fotos |
| `cercanos` | `nearby_places` | Playas y lugares cercanos con coords Google Maps |
| `publicite` | `advertising_contacts` | Formulario "publicite aquí" |

#### Tablas nuevas (no existen en legacy)

- **`media`** (polimórfica): `mediable_id`, `mediable_type`, `path`, `alt`, `sort_order`, `type` (image|video).
- **`pages`**: CRUD genérico para páginas estáticas. Campos: `slug`, `title`, `content` (rich text), `meta_description`, `published`.
- **`contact_messages`**: formulario de contacto persistido (hoy solo va por mail).
- **`surveys`**: para soportar múltiples encuestas (hoy hay una sola implícita).
- **`newsletter_campaigns`**: `subject`, `body_html`, `body_text`, `sent_at`, `sent_count`.
- Tablas de Spatie: `roles`, `permissions`, `model_has_roles`, `role_has_permissions`, `model_has_permissions`.

#### Tablas que se descartan

| Legacy | Motivo |
|---|---|
| `clubdeamigos` | Módulo dado de baja |
| `clientes` | Relacionado a MercadoPago (dado de baja) |
| `clima`, `clima_extendido` | Se reemplaza por Open-Meteo en tiempo real, sin histórico en DB |
| `categoria`, `img_articulos` | Redundantes — reemplazados por FKs propias y `media` polimórfico |
| `noticias` | Vacía, stub abandonado |
| `feeds` | Agregador RSS no usado |
| `log_dispositivos`, `logs_any` | Logs de apps móviles, fuera de scope |

#### Tablas estándar Laravel

`users`, `password_reset_tokens`, `sessions`, `cache`, `jobs`, `failed_jobs`.

---

## 6. Estrategia ETL (legacy → nuevo)

Comandos Artisan, un comando por módulo, idempotentes (rerun no duplica data).

### Flujo

1. Levantar Laravel con MariaDB vacía: `php artisan migrate`.
2. Levantar contenedor `legacy_db` (profile `etl`) con el dump legacy restaurado.
3. Ejecutar `php artisan etl:all` que dispara en orden:
   - `etl:users` → users + roles
   - `etl:news-categories`
   - `etl:news` → novedades
   - `etl:events` → agenda + seed rows para Tejo y Primavera
   - `etl:event-regs` → tejofiesta + primavera → registrations
   - `etl:lodgings` → hospedaje + imágenes
   - `etl:venues` → gourmet + nocturnos + imágenes
   - `etl:classifieds` → clasificados + imágenes + contacts
   - `etl:recipes`
   - `etl:tides`
   - `etl:rentals`
   - `etl:service-providers`
   - `etl:gallery` → imagenes
   - `etl:nearby` → cercanos
   - `etl:useful-info` → informacionutil
   - `etl:newsletter` (con validación de mails)
   - `etl:surveys` → encuesta
   - `etl:ad-contacts` → publicite
   - `etl:files` → copia `htdocs/imagenes/**` a `storage/app/public/legacy/`
4. Ejecutar `php artisan etl:verify` que imprime reporte de conteos legacy vs. nuevo, filas descartadas y archivos faltantes.
5. Apagar `legacy_db` cuando pasa OK.

### Patrón de cada comando

- `chunk(500)` para no cargar todo en memoria (la tabla `log_dispositivos` tiene 240k filas pero esa no se migra; otras llegan a miles).
- `updateOrCreate(['legacy_id' => $row->xx_id], [...])` para idempotencia.
- Helper `toUtf8()` para convertir texto `latin1` → `utf8mb4` (si no, queda "Ã¡" en vez de "á").
- Parseo defensivo de fechas `varchar` con fallback a null + log.
- Validación de mails (regex + MX opcional) en newsletter y contacts.
- Conexión legacy vía `DB::connection('legacy')` configurada en `config/database.php`.

### Reporte de `etl:verify`

Tabla con: `Tabla | Legacy | Nuevo | Descartadas | Match`, más un listado de
archivos referenciados en DB pero faltantes en disco (escrito a
`logs/etl-missing-files.log`).

---

## 7. Routing

### `routes/web.php` — público

```
GET  /                                    HomeController@index
GET  /novedades                           NewsController@index
GET  /novedades/{slug}                    NewsController@show
GET  /eventos                             EventsController@index
GET  /eventos/{slug}                      EventsController@show
POST /eventos/{slug}/inscripcion          EventsController@register
GET  /hospedajes                          LodgingsController@index
GET  /hospedajes/detalle/{slug}           LodgingsController@show
GET  /gastronomia                         VenuesController@index    (?tipo=gourmet|noche)
GET  /gastronomia/{slug}                  VenuesController@show
GET  /alquileres                          RentalsController@index
GET  /alquileres/{slug}                   RentalsController@show
GET  /clasificados                        ClassifiedsController@index
GET  /clasificados/{slug}                 ClassifiedsController@show
POST /clasificados/{slug}/contacto        ClassifiedsController@contact
GET  /servicios                           ServiceProvidersController@index
GET  /servicios/{slug}                    ServiceProvidersController@show
GET  /recetas                             RecipesController@index
GET  /recetas/{slug}                      RecipesController@show
GET  /mareas                              TidesController@index
GET  /clima                               WeatherController@index
GET  /galeria                             GalleryController@index
GET  /lugares-cercanos                    NearbyPlacesController@index
GET  /informacion-util                    UsefulInfoController@index
GET  /encuesta                            SurveysController@show
POST /encuesta                            SurveysController@store
GET  /contacto                            ContactController@show
POST /contacto                            ContactController@store
GET  /publicite                           AdvertiseController@show
POST /publicite                           AdvertiseController@store
POST /newsletter/suscripcion              NewsletterController@subscribe
GET  /newsletter/confirmar/{token}        NewsletterController@confirm
GET  /newsletter/baja/{token}             NewsletterController@unsubscribe
GET  /{page:slug}                         PagesController@show      (fallback final)
```

### `routes/admin.php` — panel admin

Prefijo `/admin`, middleware `auth` + `verified`. Recursos Laravel (`Route::resource`)
para cada módulo. Gestión de users/roles restringida a `role:admin`.

### `routes/api.php` — API pública v1

Prefijo `/api/v1`. Endpoints de lectura para weather, tides, events, news,
lodgings, venues, points-of-interest. Respuestas con `JsonResource`, envelope
`{ data, meta }`, rate limit `throttle:api` (60 req/min). Documentada con
OpenAPI/Swagger.

---

## 8. Autenticación y roles

### Stack

- **Laravel Breeze** (Blade) — login, logout, password reset. Sin registro público.
- **`spatie/laravel-permission`** — roles + permisos en DB, cacheados en Redis.

### Tres roles

| Rol | Capacidades |
|---|---|
| **admin** | Todo. Gestiona usuarios, roles, permisos y contenido. Único que ve `/admin/users`. |
| **editor** | CRUD completo de **contenido** (19 módulos). No gestiona usuarios ni configuración. |
| **moderator** | Solo `view` + `delete` en módulos moderables (classifieds, contact_messages, event_registrations, newsletter_subscribers). |

### Permisos

Patrón `{module}.{action}` con `actions = [view, create, update, delete]`,
seedeados por módulo. Ej: `news.view`, `news.create`, `news.update`, `news.delete`.

Una Policy por modelo que delega a los permisos:

```
class NewsPolicy {
    public function viewAny(User $u): bool { return $u->can('news.view'); }
    public function create(User $u): bool  { return $u->can('news.create'); }
    public function update(User $u, News $n): bool { return $u->can('news.update'); }
    public function delete(User $u, News $n): bool { return $u->can('news.delete'); }
}
```

Controllers admin usan `$this->authorizeResource(News::class, 'news')`.

### Migración de los usuarios legacy

- Crear users en `users` con `email`, `name`, `last_name` copiados.
- `password` = bcrypt de token random + flag `force_password_reset = true`.
- Middleware `ForcePasswordReset` intercepta y obliga a `/password/reset` en primer login.
- Rol `admin` asignado al usuario `administrator` existente.

---

## 9. Stack Docker

### Servicios

Seis activos + uno efímero (profile `etl`):

| Servicio | Imagen | Rol |
|---|---|---|
| `nginx` | `nginx:1.27-alpine` | Reverse proxy, assets estáticos |
| `app` | `docker/php/Dockerfile` (target `dev` o `prod`) | Laravel FPM |
| `worker` | misma imagen, `php artisan queue:work` | Procesa cola |
| `scheduler` | misma imagen, `php artisan schedule:work` | Cron jobs |
| `db` | `mariadb:11` | DB nueva |
| `redis` | `redis:7-alpine` | Cache + queue + sessions |
| `legacy_db` | `mariadb:10.6`, profile `etl` | DB legacy para ETL, efímero |

### Dockerfile multi-stage

- **`base`**: PHP 8.3 FPM Alpine + extensiones (`pdo_mysql`, `gd`, `redis`, `intl`, `zip`, `bcmath`, `exif`, `pcntl`, `mbstring`) + Composer.
- **`dev`**: base + Node/npm + Xdebug para debugging local.
- **`prod`**: base + código copiado + `composer install --no-dev --optimize-autoloader` + caches Artisan.

### Volúmenes persistentes

- `mariadb_data` — DB nueva.
- `redis_data` — AOF de Redis.
- `app_storage` — `storage/` de Laravel (uploads, logs, cache).
- `legacy_db_data` — DB legacy (solo durante ETL).

### Diferencias dev vs. prod

| Aspecto | Dev | Prod |
|---|---|---|
| Target | `dev` | `prod` |
| Código | bind mount | copiado en la imagen |
| `APP_DEBUG` | `true` | `false` |
| Vite | `npm run dev` (HMR) | `npm run build` pre-compilado |
| HTTPS | no | Traefik/Caddy + Let's Encrypt delante del nginx |
| Backups | no | cron externo: `mysqldump` + snapshot de `app_storage` |
| Monitoring | no | Laravel Pulse en `/admin/pulse` + Sentry |

### Uso

```
docker compose up -d                                 # dev
docker compose exec app php artisan migrate --seed   # DB inicial
docker compose --profile etl up -d legacy_db         # levantar DB legacy
docker compose exec app php artisan etl:all          # correr ETL
docker compose exec app php artisan etl:verify       # validar
docker compose --profile etl down legacy_db          # apagar legacy
APP_TARGET=prod APP_ENV=production docker compose up -d --build   # prod
```

---

## 10. Integraciones externas

### Clima — Open-Meteo

Gratis, sin API key, sin límites. Comando `SyncWeatherCommand` cada 30 min
(scheduler) pega a Open-Meteo con coords `(-41.05, -62.82)`, guarda en Redis
con TTL 1h. Controllers web y API leen solo de Redis. Fallback sync con
timeout 3s si Redis no tiene data. Sin histórico en DB.

### Newsletter — Resend (default)

- `resend/resend-laravel`. Free tier: 3000 mails/mes, 100/día.
- Doble opt-in con token de confirmación.
- Campañas: admin redacta `NewsletterCampaign` (subject + HTML + text) y dispara
  `SendCampaign` job que procesa subscribers confirmados en chunks de 100.
- Cada mail incluye link `/newsletter/baja/{token}` único por subscriber.

### Mail transaccional

Mismo Resend. Usa la cola Redis para no bloquear requests. Cubre:
confirmación newsletter, contacto, publicite, contacto de clasificados,
password reset, notificaciones admin.

Config: `MAIL_MAILER=log` en dev (mails al log), `MAIL_MAILER=resend` en prod.

### Mareas

CRUD manual en admin + importador CSV para carga anual masiva. No hay
API pública estable del SHN, así que nada de scrapping automático.

### Mapas — Leaflet + OpenStreetMap

Gratis, sin API key. Modelos con coordenadas (`lodgings`, `venues`,
`service_providers`, `nearby_places`, `useful_info`, `classifieds`) guardan
`latitude decimal(10,7)` + `longitude decimal(10,7)`. En el ETL, parsear o
re-geocodificar los `googlemaps` varchar del legacy.

---

## 11. Fases de construcción

| Fase | Alcance | Estimado | Verificable |
|---|---|---|---|
| 1. Fundación | Laravel 11 + Docker stack + Breeze + Spatie + CI básico | 1 sem | Login admin seed en `http://localhost:8080` |
| 2. Schema | Todas las migraciones + factories + seeders demo | 1 sem | `migrate:fresh --seed` con data coherente |
| 3. ETL | 19 comandos `etl:*` + `etl:files` + `etl:verify` | 1-1.5 sem | `etl:verify` con <1% descarte injustificado |
| 4. Admin | 17 CRUDs con policies + tests | 2.5 sem | Admin carga/edita contenido sin tocar DB |
| 5. Público | Controllers + Blade sin diseño final | 2 sem | Navegación completa funcional |
| 6. API v1 | Endpoints lectura + Resources + Swagger | 0.5 sem | curl a cada endpoint devuelve JSON consistente |
| 7. Tests/pulido | 80% coverage + Dusk + performance | 1 sem | Suite verde, coverage ≥80% |
| 8. Deploy | VPS + HTTPS + backups + monitoring | 0.5 sem | Prod con HTTPS y data real del ETL |
| 9. Diseño | Handoff a Claude Design + ajustes | TBD | — |

**Total ~2-2.5 meses** con 1 dev full-time. Con 2 devs en paralelo
(admin + público) baja a **~6 semanas**.

### Orden de CRUDs en Fase 4 (admin)

1. Users + Roles (prerequisito).
2. Pages + Gallery + Useful Info + Nearby Places (CRUDs simples, validan patrón).
3. News + News Categories.
4. Events + Event Registrations.
5. Lodgings + Venues + Rentals (comparten patrón de imágenes múltiples).
6. Classifieds + Classified Contacts.
7. Service Providers + Recipes.
8. Tides (con importador CSV).
9. Newsletter (subscribers + campaigns + envío por cola).
10. Surveys + Contact Messages + Advertising Contacts.

### Orden de páginas en Fase 5 (público)

1. Home + layouts + navegación.
2. Páginas estáticas (pages).
3. News (listado + detail).
4. Events (listado + detail + registro si aplica).
5. Lodgings + Venues + Rentals + Classifieds + Service Providers.
6. Recipes + Gallery + Nearby Places + Useful Info.
7. Tides + Weather (Open-Meteo).
8. Contacto + Publicite + Survey + Newsletter subscribe.
9. Mapa global con markers (Leaflet).
10. `sitemap.xml` + `robots.txt` + meta tags SEO.

---

## 12. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Fechas legacy en `varchar` con formatos inconsistentes | Data de eventos/newsletter no parsea | ETL con parseo defensivo + log de descartes + revisión manual del reporte |
| Encoding mezclado (`latin1` + `utf8`) en dump | Caracteres corruptos ("Ã¡") en destino | Helper `toUtf8()` centralizado, tests con strings conocidos |
| Archivos físicos referenciados pero faltantes | Imágenes rotas en sitio público | `etl:verify` lista faltantes; admin puede re-subir |
| Passwords SHA1 de admins legacy | No se pueden rehashear a bcrypt | Forzar reset en primer login vía middleware |
| Módulos poco documentados (servicios, cercanos, imágenes) | Malinterpretación del propósito | Confirmados con stakeholder durante brainstorming |
| Open-Meteo caído durante un request | Sin datos de clima | Redis cache con TTL 1h + fallback sync con timeout 3s |
| Queue worker cae sin ser advertido | Mails / ETL jobs no procesados | `restart: unless-stopped` en compose + Laravel Pulse monitoreando queue |
| VPS único sin alta disponibilidad | Sitio caído si se cae el server | Backups diarios + documentación de restore; HA está fuera de scope inicial |

---

## 13. Decisiones abiertas para el stakeholder

Estas tres fueron asumidas como defaults sensatos durante el brainstorming.
Se confirman antes de pasar a plan de implementación:

1. **Newsletter:** sistema de campañas integrado desde el admin (vs. solo
   captura + export CSV a herramienta externa). Default elegido: integrado.
2. **Mareas:** CRUD manual + importador CSV anual (vs. scrapping SHN).
   Default elegido: manual + CSV.
3. **Mapas:** Leaflet + OpenStreetMap (vs. Google Maps JS API). Default
   elegido: Leaflet.

---

## 14. Próximos pasos

1. Revisión del spec por parte del stakeholder.
2. Invocar skill `writing-plans` para generar el plan de implementación
   detallado (tasks por fase, dependencias, criterios de aceptación).
3. Kickoff Fase 1.
