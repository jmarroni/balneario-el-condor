# Fase 2 — Schema: Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Crear el schema Laravel-idiomático completo (23 tablas nuevas + modelos Eloquent + factories + demo seeders) que sirva como destino del ETL en Fase 3. Entregable: `php artisan migrate:fresh --seed` deja la DB con data demo coherente, todos los tests de migración y modelo en verde.

**Architecture:** Una migración por tabla con convenciones Laravel (plural, snake_case, sin prefijos, charset `utf8mb4`, FKs reales, `legacy_id` nullable para trazabilidad ETL). Un modelo Eloquent por tabla con `$fillable`, `$casts`, relaciones explícitas. Sistema `media` polimórfico para imágenes (reemplaza columnas `imagen0..4` y tablas `*_imagenes` del legacy). Factories con Faker en español argentino. Demo seeders que crean ~10 registros por módulo para poder explorar el admin antes del ETL real.

**Tech Stack:** Laravel 11, MariaDB 11, Eloquent, Faker con locale `es_AR`, PHPUnit.

---

## Convenciones (aplica a TODAS las tareas)

- **Tablas:** inglés plural, `snake_case`, charset `utf8mb4`, engine `InnoDB`.
- **IDs:** `$table->id()` (bigint unsigned).
- **FKs:** `$table->foreignId('x_id')->constrained()->cascadeOnDelete()` o `->nullOnDelete()` según aplique.
- **Timestamps:** `$table->timestamps()` en TODAS las tablas.
- **Soft deletes:** `$table->softDeletes()` en tablas con contenido editable (news, events, lodgings, venues, rentals, classifieds, service_providers, recipes, pages). NO en tablas de solo-lectura o efímeras (media, sessions, newsletter_subscribers, contact_messages, survey_responses, etc.).
- **Legacy trace:** `$table->unsignedBigInteger('legacy_id')->nullable()->unique();` en cada tabla migrada desde el legacy (todas excepto `media`, `pages`, `contact_messages`, `newsletter_campaigns`).
- **Slugs:** `$table->string('slug', 255)->unique();` indexed para lookup por slug.
- **Índices:** agregar `->index()` en columnas de filtrado/búsqueda típicas (`published_at`, `sort_order`, `category_id`, `views`).
- **Coordenadas:** `$table->decimal('latitude', 10, 7)->nullable()` + `$table->decimal('longitude', 10, 7)->nullable()` en tablas con mapas.

## Convención de modelos

Cada modelo Eloquent:
- Extiende `Model` (o `Authenticatable` para User).
- Declara `protected $fillable = [...]` con TODAS las columnas editables (no usar `$guarded = []`).
- Declara `protected function casts(): array` con casts explícitos (`'published_at' => 'datetime'`, `'accepts_registrations' => 'boolean'`, `'registration_data' => 'array'`, etc.).
- Usa `SoftDeletes` trait donde aplique.
- Define relaciones Eloquent: `belongsTo`, `hasMany`, `morphMany`, `morphTo`.
- Factory accesible via `use HasFactory;`.

## Convención de factories

- Locale `es_AR` configurado en `config/app.php` o en el factory.
- Datos coherentes con turismo costero argentino: nombres de playas, fiestas patronales, recetas locales.
- NO usar `Str::random()` — usar Faker para realismo.
- Factories definen defaults realistas; los tests/seeders overriden específicos.

## Convención de demo seeders

- Cada seeder crea **10 registros** del módulo usando su factory.
- Seeder es idempotente (usar `updateOrCreate` o limpiar con `truncate()` al comenzar — preferir truncate durante demo).
- NO usar `RefreshDatabase` en seeders — son idempotentes por sí.

---

## File Structure

```
app/
├── app/
│   └── Models/
│       ├── Media.php                                    ← CREATE (polimórfico)
│       ├── News.php                                     ← CREATE
│       ├── NewsCategory.php                             ← CREATE
│       ├── Event.php                                    ← CREATE
│       ├── EventRegistration.php                        ← CREATE
│       ├── Lodging.php                                  ← CREATE
│       ├── Venue.php                                    ← CREATE
│       ├── Rental.php                                   ← CREATE
│       ├── Classified.php                               ← CREATE
│       ├── ClassifiedCategory.php                       ← CREATE
│       ├── ClassifiedContact.php                        ← CREATE
│       ├── ServiceProvider.php                          ← CREATE  (renombrar a avoid clash con Laravel ServiceProvider → usar `LocalServiceProvider`)
│       ├── Recipe.php                                   ← CREATE
│       ├── Tide.php                                     ← CREATE
│       ├── GalleryImage.php                             ← CREATE
│       ├── NearbyPlace.php                              ← CREATE
│       ├── UsefulInfo.php                               ← CREATE
│       ├── Page.php                                     ← CREATE
│       ├── Survey.php                                   ← CREATE
│       ├── SurveyResponse.php                           ← CREATE
│       ├── NewsletterSubscriber.php                     ← CREATE
│       ├── NewsletterCampaign.php                       ← CREATE
│       ├── ContactMessage.php                           ← CREATE
│       └── AdvertisingContact.php                       ← CREATE
├── database/
│   ├── factories/
│   │   ├── MediaFactory.php                             ← CREATE
│   │   ├── NewsFactory.php + NewsCategoryFactory.php    ← CREATE
│   │   ├── EventFactory.php + EventRegistrationFactory  ← CREATE
│   │   ├── LodgingFactory.php + VenueFactory + RentalFactory   ← CREATE
│   │   ├── ClassifiedFactory + ClassifiedCategoryFactory + ClassifiedContactFactory   ← CREATE
│   │   ├── LocalServiceProviderFactory + RecipeFactory + GalleryImageFactory   ← CREATE
│   │   ├── NearbyPlaceFactory + UsefulInfoFactory + PageFactory + TideFactory   ← CREATE
│   │   ├── SurveyFactory + SurveyResponseFactory       ← CREATE
│   │   ├── NewsletterSubscriberFactory + NewsletterCampaignFactory   ← CREATE
│   │   └── ContactMessageFactory + AdvertisingContactFactory   ← CREATE
│   ├── migrations/
│   │   ├── 2026_04_22_000001_create_media_table.php     ← CREATE
│   │   ├── 2026_04_22_000010_create_news_categories_table.php   ← CREATE
│   │   ├── 2026_04_22_000011_create_news_table.php      ← CREATE
│   │   ├── 2026_04_22_000020_create_events_table.php    ← CREATE
│   │   ├── 2026_04_22_000021_create_event_registrations_table.php   ← CREATE
│   │   ├── 2026_04_22_000030_create_lodgings_table.php  ← CREATE
│   │   ├── 2026_04_22_000031_create_venues_table.php    ← CREATE
│   │   ├── 2026_04_22_000032_create_rentals_table.php   ← CREATE
│   │   ├── 2026_04_22_000040_create_classified_categories_table.php   ← CREATE
│   │   ├── 2026_04_22_000041_create_classifieds_table.php   ← CREATE
│   │   ├── 2026_04_22_000042_create_classified_contacts_table.php   ← CREATE
│   │   ├── 2026_04_22_000050_create_service_providers_table.php   ← CREATE
│   │   ├── 2026_04_22_000051_create_recipes_table.php   ← CREATE
│   │   ├── 2026_04_22_000052_create_gallery_images_table.php   ← CREATE
│   │   ├── 2026_04_22_000060_create_nearby_places_table.php   ← CREATE
│   │   ├── 2026_04_22_000061_create_useful_info_table.php   ← CREATE
│   │   ├── 2026_04_22_000062_create_pages_table.php     ← CREATE
│   │   ├── 2026_04_22_000063_create_tides_table.php     ← CREATE
│   │   ├── 2026_04_22_000070_create_surveys_table.php   ← CREATE
│   │   ├── 2026_04_22_000071_create_survey_responses_table.php   ← CREATE
│   │   ├── 2026_04_22_000080_create_newsletter_subscribers_table.php   ← CREATE
│   │   ├── 2026_04_22_000081_create_newsletter_campaigns_table.php   ← CREATE
│   │   ├── 2026_04_22_000090_create_contact_messages_table.php   ← CREATE
│   │   └── 2026_04_22_000091_create_advertising_contacts_table.php   ← CREATE
│   └── seeders/
│       ├── DatabaseSeeder.php                           ← MODIFY (agregar llamadas a DemoDataSeeder)
│       └── DemoDataSeeder.php                           ← CREATE (orquesta los demo seeders por módulo)
└── tests/
    └── Feature/
        └── Models/
            ├── MediaPolymorphicTest.php                 ← CREATE
            ├── NewsModelTest.php                        ← CREATE
            ├── EventModelTest.php                       ← CREATE
            ├── LodgingModelTest.php                     ← CREATE
            └── MigrationRollbackTest.php                ← CREATE (tests up/down limpio de todas)
```

---

## Task 1: Media polimórfica (foundation)

**Files:**
- Create: `app/database/migrations/2026_04_22_000001_create_media_table.php`
- Create: `app/app/Models/Media.php`
- Create: `app/database/factories/MediaFactory.php`
- Create: `app/tests/Feature/Models/MediaPolymorphicTest.php`

- [ ] **Step 1: Escribir test polimórfico (falla)**

Create `app/tests/Feature/Models/MediaPolymorphicTest.php`:

```php
<?php

namespace Tests\Feature\Models;

use App\Models\Media;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaPolymorphicTest extends TestCase
{
    use RefreshDatabase;

    public function test_media_belongs_to_any_mediable_model(): void
    {
        $category = NewsCategory::factory()->create();
        $news = News::factory()->create(['news_category_id' => $category->id]);

        $media = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'uploads/news/test.jpg',
            'alt'           => 'Test image',
            'type'          => 'image',
            'sort_order'    => 0,
        ]);

        $this->assertTrue($media->mediable->is($news));
        $this->assertCount(1, $news->media);
    }

    public function test_media_sort_order_default_zero(): void
    {
        $category = NewsCategory::factory()->create();
        $news = News::factory()->create(['news_category_id' => $category->id]);

        $media = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'uploads/news/test.jpg',
        ]);

        $this->assertSame(0, $media->sort_order);
    }
}
```

- [ ] **Step 2: Run — FAIL**

```bash
cd /home/juan/sitios/balneario-el-condor/app
docker compose exec app php artisan test tests/Feature/Models/MediaPolymorphicTest.php
```

Expected: FAIL (modelos no existen).

- [ ] **Step 3: Crear migración media**

Create `app/database/migrations/2026_04_22_000001_create_media_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->string('path', 500);
            $table->string('alt', 255)->nullable();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
```

- [ ] **Step 4: Crear modelo Media**

Create `app/app/Models/Media.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'path',
        'alt',
        'type',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
```

- [ ] **Step 5: Crear MediaFactory**

Create `app/database/factories/MediaFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'path'       => 'uploads/demo/' . fake()->uuid() . '.jpg',
            'alt'        => fake()->sentence(4),
            'type'       => 'image',
            'sort_order' => 0,
            // mediable_id y mediable_type se proveen al llamar al factory
        ];
    }
}
```

- [ ] **Step 6: Correr tests — todavía falla porque News/NewsCategory aún no existen**

Expected: test FAIL con "Class 'App\\Models\\News' not found". Eso es esperado en este punto — el test completo pasará después de Task 2. Saltearlo temporalmente con `$this->markTestSkipped()` NO — lo dejamos y lo corremos después de Task 2.

Correr igual para verificar migración:

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
```

Expected: tabla `media` se crea sin error.

- [ ] **Step 7: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): tabla media polimórfica + modelo + factory"
```

---

## Task 2: News (novedades) + News Categories

**Files:**
- Create: migraciones `create_news_categories_table.php`, `create_news_table.php`
- Create: modelos `NewsCategory.php`, `News.php`
- Create: factories `NewsCategoryFactory.php`, `NewsFactory.php`
- Create: test `NewsModelTest.php`

- [ ] **Step 1: Migración news_categories**

Create `app/database/migrations/2026_04_22_000010_create_news_categories_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_categories');
    }
};
```

- [ ] **Step 2: Migración news**

Create `app/database/migrations/2026_04_22_000011_create_news_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->text('body');
            $table->string('video_url', 500)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
```

- [ ] **Step 3: Modelos NewsCategory y News**

Create `app/app/Models/NewsCategory.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewsCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'legacy_id'];

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
```

Create `app/app/Models/News.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'news_category_id',
        'title',
        'slug',
        'body',
        'video_url',
        'published_at',
        'views',
        'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'views'        => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(NewsCategory::class, 'news_category_id');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

- [ ] **Step 4: Factories**

Create `app/database/factories/NewsCategoryFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsCategoryFactory extends Factory
{
    protected $model = NewsCategory::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Turismo', 'Eventos', 'Temporada', 'Mantenimiento', 'Pesca', 'Gastronomía']);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
        ];
    }
}
```

Create `app/database/factories/NewsFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);
        return [
            'news_category_id' => NewsCategory::factory(),
            'title'            => $title,
            'slug'             => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'body'             => fake()->paragraphs(5, true),
            'video_url'        => fake()->boolean(20) ? fake()->url() : null,
            'published_at'     => fake()->dateTimeBetween('-2 years', 'now'),
            'views'            => fake()->numberBetween(0, 5000),
        ];
    }
}
```

- [ ] **Step 5: Test del modelo**

Create `app/tests/Feature/Models/NewsModelTest.php`:

```php
<?php

namespace Tests\Feature\Models;

use App\Models\Media;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_belongs_to_category(): void
    {
        $category = NewsCategory::factory()->create();
        $news = News::factory()->create(['news_category_id' => $category->id]);

        $this->assertTrue($news->category->is($category));
    }

    public function test_news_has_many_media_ordered_by_sort_order(): void
    {
        $news = News::factory()->create();

        $second = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'p2.jpg',
            'sort_order'    => 2,
        ]);
        $first = Media::factory()->create([
            'mediable_id'   => $news->id,
            'mediable_type' => News::class,
            'path'          => 'p1.jpg',
            'sort_order'    => 1,
        ]);

        $ordered = $news->media->pluck('path')->all();
        $this->assertSame(['p1.jpg', 'p2.jpg'], $ordered);
    }

    public function test_published_at_is_datetime_cast(): void
    {
        $news = News::factory()->create(['published_at' => '2026-04-21 10:00:00']);
        $this->assertInstanceOf(\DateTimeInterface::class, $news->published_at);
    }

    public function test_soft_delete_preserves_row(): void
    {
        $news = News::factory()->create();
        $news->delete();

        $this->assertSoftDeleted('news', ['id' => $news->id]);
    }
}
```

- [ ] **Step 6: Correr migraciones + tests**

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
docker compose exec app php artisan test tests/Feature/Models/NewsModelTest.php
docker compose exec app php artisan test tests/Feature/Models/MediaPolymorphicTest.php
```

Expected: ambos tests PASS.

- [ ] **Step 7: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): news + news_categories (modelos, factories, tests)"
```

---

## Task 3: Events + Event Registrations

**Files:**
- Create: migraciones `create_events_table.php`, `create_event_registrations_table.php`
- Create: modelos `Event.php`, `EventRegistration.php`
- Create: factories `EventFactory.php`, `EventRegistrationFactory.php`
- Create: test `EventModelTest.php`

- [ ] **Step 1: Migración events**

Create `app/database/migrations/2026_04_22_000020_create_events_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('location', 500)->nullable();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->boolean('featured')->default(false)->index();
            $table->boolean('accepts_registrations')->default(false);
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('external_url', 500)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
```

- [ ] **Step 2: Migración event_registrations**

Create `app/database/migrations/2026_04_22_000021_create_event_registrations_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('last_name', 200)->nullable();
            $table->string('email', 200)->nullable()->index();
            $table->string('phone', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('city', 200)->nullable();
            $table->json('extra_data')->nullable();  // campos custom por evento (concursantes, cena, etc.)
            $table->text('comments')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
```

- [ ] **Step 3: Modelos**

Create `app/app/Models/Event.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'location',
        'starts_at', 'ends_at', 'all_day', 'featured',
        'accepts_registrations', 'sort_order', 'external_url', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at'              => 'datetime',
            'ends_at'                => 'datetime',
            'all_day'                => 'boolean',
            'featured'               => 'boolean',
            'accepts_registrations'  => 'boolean',
            'sort_order'             => 'integer',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Create `app/app/Models/EventRegistration.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'name', 'last_name', 'email', 'phone',
        'province', 'city', 'extra_data', 'comments', 'ip_address', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'extra_data' => 'array',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
```

- [ ] **Step 4: Factories**

Create `app/database/factories/EventFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Fiesta de la Primavera',
            'Fiesta del Tejo',
            'Encuentro de Pescadores',
            'Feria de Artesanos',
            'Festival de Música de Verano',
            'Torneo de Vóley Playa',
        ]) . ' ' . fake()->year();

        $starts = fake()->dateTimeBetween('-1 year', '+6 months');

        return [
            'title'                 => $title,
            'slug'                  => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description'           => fake()->paragraphs(3, true),
            'location'              => fake()->randomElement(['Costanera', 'Plaza Central', 'Playa Principal', 'Muelle']),
            'starts_at'             => $starts,
            'ends_at'               => (clone $starts)->modify('+3 hours'),
            'all_day'               => fake()->boolean(20),
            'featured'              => fake()->boolean(30),
            'accepts_registrations' => fake()->boolean(50),
            'sort_order'            => 0,
        ];
    }
}
```

Create `app/database/factories/EventRegistrationFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        return [
            'event_id'   => Event::factory(),
            'name'       => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'email'      => fake()->safeEmail(),
            'phone'      => fake()->phoneNumber(),
            'province'   => fake()->randomElement(['Río Negro', 'Buenos Aires', 'La Pampa', 'Neuquén']),
            'city'       => fake()->city(),
            'extra_data' => [
                'attendees'   => fake()->numberBetween(1, 5),
                'accommodation' => fake()->boolean(),
            ],
            'comments'   => fake()->optional(0.3)->sentence(),
            'ip_address' => fake()->ipv4(),
        ];
    }
}
```

- [ ] **Step 5: Test del modelo**

Create `app/tests/Feature/Models/EventModelTest.php`:

```php
<?php

namespace Tests\Feature\Models;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_has_many_registrations(): void
    {
        $event = Event::factory()->create();
        EventRegistration::factory()->count(3)->create(['event_id' => $event->id]);

        $this->assertCount(3, $event->registrations);
    }

    public function test_registration_extra_data_is_array_cast(): void
    {
        $reg = EventRegistration::factory()->create();
        $this->assertIsArray($reg->extra_data);
    }

    public function test_cascade_delete_removes_registrations(): void
    {
        $event = Event::factory()->create();
        EventRegistration::factory()->count(2)->create(['event_id' => $event->id]);

        $event->forceDelete();

        $this->assertDatabaseCount('event_registrations', 0);
    }

    public function test_event_soft_delete(): void
    {
        $event = Event::factory()->create();
        $event->delete();
        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }
}
```

- [ ] **Step 6: Correr**

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
docker compose exec app php artisan test tests/Feature/Models/EventModelTest.php
```

Expected: 4 tests PASS.

- [ ] **Step 7: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): events + event_registrations con extra_data JSON"
```

---

## Task 4: Lodgings + Venues + Rentals

**Files:**
- Create: 3 migraciones + 3 modelos + 3 factories + 1 test integrado

- [ ] **Step 1: Migración lodgings**

Create `app/database/migrations/2026_04_22_000030_create_lodgings_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lodgings', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['hotel', 'casa', 'camping', 'hostel', 'other'])->index();
            $table->string('website', 255)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lodgings');
    }
};
```

- [ ] **Step 2: Migración venues**

Create `app/database/migrations/2026_04_22_000031_create_venues_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->enum('category', ['gourmet', 'nightlife'])->index();
            $table->text('description')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
```

- [ ] **Step 3: Migración rentals**

Create `app/database/migrations/2026_04_22_000032_create_rentals_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->unsignedInteger('places')->nullable();   // plazas
            $table->string('contact_name', 200)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('address', 500)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
```

- [ ] **Step 4: Modelos Lodging, Venue, Rental**

Los 3 comparten estructura similar. Crearlos con `HasFactory`, `SoftDeletes`, `morphMany(Media)`.

Create `app/app/Models/Lodging.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lodging extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'type',
        'website', 'email', 'phone', 'address',
        'latitude', 'longitude', 'views', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'views'     => 'integer',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Create `app/app/Models/Venue.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'category', 'description',
        'phone', 'address', 'latitude', 'longitude', 'views', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'views'     => 'integer',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Create `app/app/Models/Rental.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rental extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'places', 'contact_name',
        'phone', 'email', 'address', 'description', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'places' => 'integer',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

- [ ] **Step 5: Factories**

Create `app/database/factories/LodgingFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Lodging;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LodgingFactory extends Factory
{
    protected $model = Lodging::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Hotel Faro', 'Cabañas del Mar', 'Hostería El Cóndor',
            'Camping Municipal', 'Casa de Doña Rosa', 'Posada Vista Bahía',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);
        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => fake()->paragraphs(2, true),
            'type'        => fake()->randomElement(['hotel', 'casa', 'camping', 'hostel']),
            'website'     => fake()->optional(0.4)->url(),
            'email'       => fake()->safeEmail(),
            'phone'       => '+54 9 2920 ' . fake()->numerify('######'),
            'address'     => fake()->streetAddress() . ', El Cóndor',
            'latitude'    => fake()->latitude(-41.2, -40.9),
            'longitude'   => fake()->longitude(-62.9, -62.6),
            'views'       => fake()->numberBetween(0, 2000),
        ];
    }
}
```

Create `app/database/factories/VenueFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        $gourmetNames = ['La Cocina de Mar', 'Puerto Gourmet', 'El Rincón del Pescador', 'Parrilla Costera'];
        $nightNames   = ['Bar La Gaviota', 'Pub del Puerto', 'Sunset Lounge', 'Bodega Nocturna'];

        $category = fake()->randomElement(['gourmet', 'nightlife']);
        $name = fake()->randomElement($category === 'gourmet' ? $gourmetNames : $nightNames)
              . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'category'    => $category,
            'description' => fake()->paragraphs(2, true),
            'phone'       => '+54 9 2920 ' . fake()->numerify('######'),
            'address'     => fake()->streetAddress() . ', El Cóndor',
            'latitude'    => fake()->latitude(-41.2, -40.9),
            'longitude'   => fake()->longitude(-62.9, -62.6),
            'views'       => fake()->numberBetween(0, 1500),
        ];
    }
}
```

Create `app/database/factories/RentalFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RentalFactory extends Factory
{
    protected $model = Rental::class;

    public function definition(): array
    {
        $title = 'Alquiler ' . fake()->randomElement(['temporal', 'mensual', 'fin de semana', 'de temporada'])
               . ' ' . fake()->unique()->numberBetween(1, 999);
        return [
            'title'        => $title,
            'slug'         => Str::slug($title),
            'places'       => fake()->numberBetween(2, 10),
            'contact_name' => fake()->name(),
            'phone'        => '+54 9 2920 ' . fake()->numerify('######'),
            'email'        => fake()->safeEmail(),
            'address'      => fake()->streetAddress() . ', El Cóndor',
            'description'  => fake()->paragraph(),
        ];
    }
}
```

- [ ] **Step 6: Test integrado**

Create `app/tests/Feature/Models/LodgingModelTest.php`:

```php
<?php

namespace Tests\Feature\Models;

use App\Models\Lodging;
use App\Models\Media;
use App\Models\Rental;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LodgingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_lodging_morph_media(): void
    {
        $lodging = Lodging::factory()->create();
        Media::factory()->create([
            'mediable_id'   => $lodging->id,
            'mediable_type' => Lodging::class,
            'path'          => 'l.jpg',
        ]);

        $this->assertCount(1, $lodging->fresh()->media);
    }

    public function test_venue_has_category_and_media(): void
    {
        $venue = Venue::factory()->create(['category' => 'gourmet']);
        $this->assertSame('gourmet', $venue->category);

        Media::factory()->create([
            'mediable_id'   => $venue->id,
            'mediable_type' => Venue::class,
            'path'          => 'v.jpg',
        ]);

        $this->assertCount(1, $venue->fresh()->media);
    }

    public function test_rental_has_places_and_media(): void
    {
        $rental = Rental::factory()->create(['places' => 4]);
        $this->assertSame(4, $rental->places);

        Media::factory()->count(3)->create([
            'mediable_id'   => $rental->id,
            'mediable_type' => Rental::class,
        ]);

        $this->assertCount(3, $rental->fresh()->media);
    }

    public function test_all_three_soft_delete(): void
    {
        $l = Lodging::factory()->create(); $l->delete(); $this->assertSoftDeleted('lodgings', ['id' => $l->id]);
        $v = Venue::factory()->create();   $v->delete(); $this->assertSoftDeleted('venues',   ['id' => $v->id]);
        $r = Rental::factory()->create();  $r->delete(); $this->assertSoftDeleted('rentals',  ['id' => $r->id]);
    }
}
```

- [ ] **Step 7: Correr**

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
docker compose exec app php artisan test tests/Feature/Models/LodgingModelTest.php
```

Expected: 4 tests PASS.

- [ ] **Step 8: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): lodgings + venues + rentals con media polimórfica"
```

---

## Task 5: Classifieds (con contactos y categorías)

**Files:**
- Create: migraciones `create_classified_categories_table.php`, `create_classifieds_table.php`, `create_classified_contacts_table.php`
- Create: modelos `ClassifiedCategory.php`, `Classified.php`, `ClassifiedContact.php`
- Create: factories correspondientes

- [ ] **Step 1: Migración classified_categories**

Create `app/database/migrations/2026_04_22_000040_create_classified_categories_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classified_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 255)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classified_categories');
    }
};
```

- [ ] **Step 2: Migración classifieds**

Create `app/database/migrations/2026_04_22_000041_create_classifieds_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classifieds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classified_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->text('description');
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_email', 200)->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('video_url', 500)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classifieds');
    }
};
```

- [ ] **Step 3: Migración classified_contacts**

Create `app/database/migrations/2026_04_22_000042_create_classified_contacts_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classified_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classified_id')->constrained()->cascadeOnDelete();
            $table->string('contact_name', 100);
            $table->string('contact_email', 200);
            $table->string('contact_phone', 100)->nullable();
            $table->text('message')->nullable();
            $table->string('destination_email', 200)->nullable();  // a quién se reenvió
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classified_contacts');
    }
};
```

- [ ] **Step 4: Modelos**

Create `app/app/Models/ClassifiedCategory.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassifiedCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    public function classifieds(): HasMany
    {
        return $this->hasMany(Classified::class);
    }
}
```

Create `app/app/Models/Classified.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classified extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'classified_category_id', 'title', 'slug', 'description',
        'contact_name', 'contact_email', 'address', 'latitude', 'longitude',
        'video_url', 'views', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'views'     => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassifiedCategory::class, 'classified_category_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ClassifiedContact::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Create `app/app/Models/ClassifiedContact.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassifiedContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'classified_id', 'contact_name', 'contact_email', 'contact_phone',
        'message', 'destination_email', 'ip_address', 'legacy_id',
    ];

    public function classified(): BelongsTo
    {
        return $this->belongsTo(Classified::class);
    }
}
```

- [ ] **Step 5: Factories**

Create `app/database/factories/ClassifiedCategoryFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\ClassifiedCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClassifiedCategoryFactory extends Factory
{
    protected $model = ClassifiedCategory::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'Inmuebles', 'Vehículos', 'Empleos',
            'Servicios', 'Objetos', 'Mascotas',
        ]);
        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
        ];
    }
}
```

Create `app/database/factories/ClassifiedFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Classified;
use App\Models\ClassifiedCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClassifiedFactory extends Factory
{
    protected $model = Classified::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Vendo', 'Alquilo', 'Busco', 'Ofrezco',
        ]) . ' ' . fake()->words(3, true);

        return [
            'classified_category_id' => ClassifiedCategory::factory(),
            'title'         => $title,
            'slug'          => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description'   => fake()->paragraphs(2, true),
            'contact_name'  => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'address'       => fake()->optional(0.7)->streetAddress(),
            'latitude'      => fake()->optional(0.5)->latitude(-41.2, -40.9),
            'longitude'     => fake()->optional(0.5)->longitude(-62.9, -62.6),
            'video_url'     => fake()->optional(0.1)->url(),
            'views'         => fake()->numberBetween(0, 500),
        ];
    }
}
```

Create `app/database/factories/ClassifiedContactFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Classified;
use App\Models\ClassifiedContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassifiedContactFactory extends Factory
{
    protected $model = ClassifiedContact::class;

    public function definition(): array
    {
        return [
            'classified_id'     => Classified::factory(),
            'contact_name'      => fake()->name(),
            'contact_email'     => fake()->safeEmail(),
            'contact_phone'     => '+54 9 2920 ' . fake()->numerify('######'),
            'message'           => fake()->paragraph(),
            'destination_email' => fake()->safeEmail(),
            'ip_address'        => fake()->ipv4(),
        ];
    }
}
```

- [ ] **Step 6: Correr migraciones + smoke test**

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
docker compose exec app php artisan tinker --execute="\App\Models\Classified::factory()->has(\App\Models\ClassifiedContact::factory()->count(3), 'contacts')->create(); echo \App\Models\Classified::first()->contacts->count();"
```

Expected: output `3`.

- [ ] **Step 7: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): classifieds + categories + contacts"
```

---

## Task 6: Service Providers + Recipes + Gallery Images

**Files:**
- Create: 3 migraciones + 3 modelos + 3 factories

- [ ] **Step 1: Migración service_providers**

Create `app/database/migrations/2026_04_22_000050_create_service_providers_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('contact_name', 100)->nullable();
            $table->string('contact_email', 200)->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
```

- [ ] **Step 2: Migración recipes**

Create `app/database/migrations/2026_04_22_000051_create_recipes_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->unsignedInteger('prep_minutes')->nullable();
            $table->unsignedInteger('cook_minutes')->nullable();
            $table->string('servings', 100)->nullable();
            $table->string('cost', 100)->nullable();
            $table->text('ingredients');
            $table->text('instructions');
            $table->string('author', 200)->nullable();
            $table->date('published_on')->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
```

- [ ] **Step 3: Migración gallery_images**

Create `app/database/migrations/2026_04_22_000052_create_gallery_images_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200)->nullable();
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('path', 500);
            $table->string('thumb_path', 500)->nullable();
            $table->string('original_path', 500)->nullable();
            $table->date('taken_on')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
```

- [ ] **Step 4: Modelos**

Create `app/app/Models/ServiceProvider.php`:

Importante: el nombre `ServiceProvider` conflicta con `Illuminate\Support\ServiceProvider`. Usar namespace completo en imports donde aparezca. El modelo se llama `ServiceProvider` pero vive en `App\Models\`, así que los controllers lo referencian como `App\Models\ServiceProvider`.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description',
        'contact_name', 'contact_email', 'phone', 'address',
        'latitude', 'longitude', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Create `app/app/Models/Recipe.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'slug',
        'prep_minutes', 'cook_minutes', 'servings', 'cost',
        'ingredients', 'instructions', 'author',
        'published_on', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'prep_minutes' => 'integer',
            'cook_minutes' => 'integer',
            'published_on' => 'date',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Create `app/app/Models/GalleryImage.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description',
        'path', 'thumb_path', 'original_path',
        'taken_on', 'views', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'taken_on' => 'date',
            'views'    => 'integer',
        ];
    }
}
```

- [ ] **Step 5: Factories (los tres)**

Create `app/database/factories/ServiceProviderFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ServiceProviderFactory extends Factory
{
    protected $model = ServiceProvider::class;

    public function definition(): array
    {
        $services = ['Plomería', 'Electricidad', 'Gasista', 'Albañilería', 'Jardinería', 'Carpintería', 'Pintura'];
        $name = fake()->randomElement($services) . ' ' . fake()->lastName();
        return [
            'name'          => $name,
            'slug'          => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description'   => fake()->paragraph(),
            'contact_name'  => fake()->name(),
            'contact_email' => fake()->safeEmail(),
            'phone'         => '+54 9 2920 ' . fake()->numerify('######'),
            'address'       => fake()->streetAddress() . ', El Cóndor',
            'latitude'      => fake()->latitude(-41.2, -40.9),
            'longitude'     => fake()->longitude(-62.9, -62.6),
        ];
    }
}
```

Create `app/database/factories/RecipeFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RecipeFactory extends Factory
{
    protected $model = Recipe::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Mejillones al vapor', 'Calamares rellenos', 'Pescado a la parrilla',
            'Torta de algas', 'Empanadas de mariscos', 'Sopa de pescado',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'title'        => $title,
            'slug'         => Str::slug($title),
            'prep_minutes' => fake()->numberBetween(10, 60),
            'cook_minutes' => fake()->numberBetween(15, 120),
            'servings'     => fake()->randomElement(['2 porciones', '4 porciones', '6 porciones', '8-10 porciones']),
            'cost'         => fake()->randomElement(['Bajo', 'Medio', 'Alto']),
            'ingredients'  => "- Ingrediente 1\n- Ingrediente 2\n- Ingrediente 3",
            'instructions' => fake()->paragraphs(3, true),
            'author'       => fake()->name(),
            'published_on' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
        ];
    }
}
```

Create `app/database/factories/GalleryImageFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GalleryImageFactory extends Factory
{
    protected $model = GalleryImage::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Atardecer en la playa', 'Pescadores al amanecer', 'Faro histórico',
            'Costanera en verano', 'Olas del Atlántico', 'Flora local',
        ]) . ' ' . fake()->unique()->numberBetween(1, 9999);

        return [
            'title'         => $title,
            'slug'          => Str::slug($title),
            'description'   => fake()->optional()->sentence(),
            'path'          => 'gallery/' . fake()->uuid() . '.jpg',
            'thumb_path'    => 'gallery/thumbs/' . fake()->uuid() . '.jpg',
            'original_path' => 'gallery/original/' . fake()->uuid() . '.jpg',
            'taken_on'      => fake()->optional(0.6)->dateTimeBetween('-5 years', 'now')?->format('Y-m-d'),
            'views'         => fake()->numberBetween(0, 3000),
        ];
    }
}
```

- [ ] **Step 6: Correr migrate + smoke**

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
docker compose exec app php artisan tinker --execute="echo App\Models\ServiceProvider::factory()->count(3)->create()->count() . ' providers; ' . App\Models\Recipe::factory()->count(3)->create()->count() . ' recipes; ' . App\Models\GalleryImage::factory()->count(5)->create()->count() . ' gallery';"
```

Expected: `3 providers; 3 recipes; 5 gallery`.

- [ ] **Step 7: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): service_providers + recipes + gallery_images"
```

---

## Task 7: Nearby Places + Useful Info + Pages + Tides

**Files:**
- Create: 4 migraciones + 4 modelos + 4 factories

- [ ] **Step 1: Migración nearby_places**

Create `app/database/migrations/2026_04_22_000060_create_nearby_places_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nearby_places', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nearby_places');
    }
};
```

- [ ] **Step 2: Migración useful_info**

Create `app/database/migrations/2026_04_22_000061_create_useful_info_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('useful_info', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('phone', 100)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('address', 500)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('useful_info');
    }
};
```

- [ ] **Step 3: Migración pages**

Create `app/database/migrations/2026_04_22_000062_create_pages_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 255)->unique();
            $table->string('title', 200);
            $table->longText('content')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->boolean('published')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
```

- [ ] **Step 4: Migración tides**

Create `app/database/migrations/2026_04_22_000063_create_tides_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tides', function (Blueprint $table) {
            $table->id();
            $table->string('location', 100)->default('El Cóndor');
            $table->date('date')->index();
            $table->time('first_high')->nullable();
            $table->string('first_high_height', 20)->nullable();
            $table->time('first_low')->nullable();
            $table->string('first_low_height', 20)->nullable();
            $table->time('second_high')->nullable();
            $table->string('second_high_height', 20)->nullable();
            $table->time('second_low')->nullable();
            $table->string('second_low_height', 20)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();

            $table->unique(['location', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tides');
    }
};
```

- [ ] **Step 5: Modelos (4)**

Create `app/app/Models/NearbyPlace.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class NearbyPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'description', 'address',
        'latitude', 'longitude', 'views', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'  => 'decimal:7',
            'longitude' => 'decimal:7',
            'views'     => 'integer',
        ];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Create `app/app/Models/UsefulInfo.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsefulInfo extends Model
{
    use HasFactory;

    protected $table = 'useful_info';

    protected $fillable = [
        'title', 'phone', 'website', 'email', 'address',
        'latitude', 'longitude', 'sort_order', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'latitude'   => 'decimal:7',
            'longitude'  => 'decimal:7',
            'sort_order' => 'integer',
        ];
    }
}
```

Create `app/app/Models/Page.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['slug', 'title', 'content', 'meta_description', 'published'];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
```

Create `app/app/Models/Tide.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tide extends Model
{
    use HasFactory;

    protected $fillable = [
        'location', 'date',
        'first_high', 'first_high_height',
        'first_low', 'first_low_height',
        'second_high', 'second_high_height',
        'second_low', 'second_low_height',
        'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
```

- [ ] **Step 6: Factories (4)**

Create `app/database/factories/NearbyPlaceFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\NearbyPlace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NearbyPlaceFactory extends Factory
{
    protected $model = NearbyPlace::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Playa La Lobería', 'Bahía Creek', 'Viedma', 'Carmen de Patagones',
            'Faro Segunda Barranca', 'Río Negro - desembocadura',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'title'       => $title,
            'slug'        => Str::slug($title),
            'description' => fake()->paragraph(),
            'address'     => fake()->optional()->streetAddress(),
            'latitude'    => fake()->latitude(-41.2, -40.6),
            'longitude'   => fake()->longitude(-63.0, -62.5),
            'views'       => fake()->numberBetween(0, 1500),
        ];
    }
}
```

Create `app/database/factories/UsefulInfoFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\UsefulInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsefulInfoFactory extends Factory
{
    protected $model = UsefulInfo::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Policía', 'Bomberos', 'Hospital', 'Defensa Civil',
            'Municipalidad', 'Turismo Municipal', 'Guardavidas',
        ]);

        return [
            'title'      => $title,
            'phone'      => fake()->boolean(70) ? fake()->randomElement(['911', '100', '107']) : '+54 9 2920 ' . fake()->numerify('######'),
            'website'    => fake()->optional(0.3)->url(),
            'email'      => fake()->optional(0.5)->safeEmail(),
            'address'    => fake()->streetAddress() . ', El Cóndor',
            'latitude'   => fake()->latitude(-41.2, -40.9),
            'longitude'  => fake()->longitude(-62.9, -62.6),
            'sort_order' => fake()->numberBetween(0, 100),
        ];
    }
}
```

Create `app/database/factories/PageFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = fake()->randomElement([
            'Historia del Balneario',
            'Fauna local',
            'Lugares imperdibles',
            'Servicios disponibles',
            'Cómo llegar',
            'Preguntas frecuentes',
        ]) . ' ' . fake()->unique()->numberBetween(1, 999);

        return [
            'slug'             => Str::slug($title),
            'title'            => $title,
            'content'          => fake()->paragraphs(5, true),
            'meta_description' => fake()->sentence(12),
            'published'        => true,
        ];
    }
}
```

Create `app/database/factories/TideFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Tide;
use Illuminate\Database\Eloquent\Factories\Factory;

class TideFactory extends Factory
{
    protected $model = Tide::class;

    public function definition(): array
    {
        return [
            'location'            => 'El Cóndor',
            'date'                => fake()->unique()->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d'),
            'first_high'          => fake()->time('H:i:s'),
            'first_high_height'   => fake()->randomFloat(2, 2.0, 5.0) . ' m',
            'first_low'           => fake()->time('H:i:s'),
            'first_low_height'    => fake()->randomFloat(2, 0.2, 1.5) . ' m',
            'second_high'         => fake()->time('H:i:s'),
            'second_high_height'  => fake()->randomFloat(2, 2.0, 5.0) . ' m',
            'second_low'          => fake()->time('H:i:s'),
            'second_low_height'   => fake()->randomFloat(2, 0.2, 1.5) . ' m',
        ];
    }
}
```

- [ ] **Step 7: Correr**

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
docker compose exec app php artisan tinker --execute="echo App\Models\NearbyPlace::factory()->count(3)->create()->count() . ' nearby; ' . App\Models\UsefulInfo::factory()->count(5)->create()->count() . ' info; ' . App\Models\Page::factory()->count(4)->create()->count() . ' pages; ' . App\Models\Tide::factory()->count(3)->create()->count() . ' tides';"
```

Expected: `3 nearby; 5 info; 4 pages; 3 tides`.

- [ ] **Step 8: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): nearby_places + useful_info + pages + tides"
```

---

## Task 8: Surveys + Newsletter + Contact + Advertising

**Files:**
- Create: 6 migraciones + 6 modelos + 6 factories

- [ ] **Step 1: Migración surveys y survey_responses**

Create `app/database/migrations/2026_04_22_000070_create_surveys_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('question');
            $table->json('options');  // array of { key: int, label: string }
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
```

Create `app/database/migrations/2026_04_22_000071_create_survey_responses_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('option_key');
            $table->text('comment')->nullable();
            $table->string('email', 200)->nullable();
            $table->boolean('accepted_terms')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
```

- [ ] **Step 2: Migración newsletter_subscribers**

Create `app/database/migrations/2026_04_22_000080_create_newsletter_subscribers_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email', 200)->unique();
            $table->enum('status', ['pending', 'confirmed', 'unsubscribed'])->default('pending')->index();
            $table->string('confirmation_token', 64)->nullable()->unique();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->unsignedBigInteger('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
```

- [ ] **Step 3: Migración newsletter_campaigns**

Create `app/database/migrations/2026_04_22_000081_create_newsletter_campaigns_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 300);
            $table->longText('body_html');
            $table->longText('body_text')->nullable();
            $table->enum('status', ['draft', 'sending', 'sent'])->default('draft')->index();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('sent_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaigns');
    }
};
```

- [ ] **Step 4: Migración contact_messages y advertising_contacts**

Create `app/database/migrations/2026_04_22_000090_create_contact_messages_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('email', 200);
            $table->string('phone', 100)->nullable();
            $table->string('subject', 300)->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('read')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
```

Create `app/database/migrations/2026_04_22_000091_create_advertising_contacts_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advertising_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('last_name', 200)->nullable();
            $table->string('email', 200);
            $table->text('message');
            $table->string('zone', 200)->nullable();
            $table->boolean('read')->default(false)->index();
            $table->unsignedBigInteger('legacy_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertising_contacts');
    }
};
```

- [ ] **Step 5: Modelos (6)**

Create `app/app/Models/Survey.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'question', 'options', 'active'];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'active'  => 'boolean',
        ];
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
```

Create `app/app/Models/SurveyResponse.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id', 'option_key', 'comment',
        'email', 'accepted_terms', 'ip_address', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'option_key'     => 'integer',
            'accepted_terms' => 'boolean',
        ];
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }
}
```

Create `app/app/Models/NewsletterSubscriber.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email', 'status', 'confirmation_token',
        'subscribed_at', 'confirmed_at', 'unsubscribed_at',
        'ip_address', 'legacy_id',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at'   => 'datetime',
            'confirmed_at'    => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
```

Create `app/app/Models/NewsletterCampaign.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by_user_id', 'subject', 'body_html', 'body_text',
        'status', 'scheduled_at', 'sent_at', 'sent_count',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at'      => 'datetime',
            'sent_count'   => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
```

Create `app/app/Models/ContactMessage.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'ip_address', 'read',
    ];

    protected function casts(): array
    {
        return ['read' => 'boolean'];
    }
}
```

Create `app/app/Models/AdvertisingContact.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertisingContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'last_name', 'email', 'message', 'zone', 'read', 'legacy_id',
    ];

    protected function casts(): array
    {
        return ['read' => 'boolean'];
    }
}
```

- [ ] **Step 6: Factories (6)**

Create `app/database/factories/SurveyFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Survey;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyFactory extends Factory
{
    protected $model = Survey::class;

    public function definition(): array
    {
        return [
            'title'    => 'Encuesta ' . fake()->unique()->words(3, true),
            'question' => fake()->sentence() . '?',
            'options'  => [
                ['key' => 1, 'label' => 'Excelente'],
                ['key' => 2, 'label' => 'Bueno'],
                ['key' => 3, 'label' => 'Regular'],
                ['key' => 4, 'label' => 'Malo'],
            ],
            'active'   => true,
        ];
    }
}
```

Create `app/database/factories/SurveyResponseFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

class SurveyResponseFactory extends Factory
{
    protected $model = SurveyResponse::class;

    public function definition(): array
    {
        return [
            'survey_id'      => Survey::factory(),
            'option_key'     => fake()->numberBetween(1, 4),
            'comment'        => fake()->optional(0.3)->sentence(),
            'email'          => fake()->optional(0.4)->safeEmail(),
            'accepted_terms' => true,
            'ip_address'     => fake()->ipv4(),
        ];
    }
}
```

Create `app/database/factories/NewsletterSubscriberFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        return [
            'email'              => fake()->unique()->safeEmail(),
            'status'             => 'confirmed',
            'confirmation_token' => Str::random(40),
            'subscribed_at'      => fake()->dateTimeBetween('-2 years', 'now'),
            'confirmed_at'       => fake()->dateTimeBetween('-2 years', 'now'),
            'ip_address'         => fake()->ipv4(),
        ];
    }
}
```

Create `app/database/factories/NewsletterCampaignFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\NewsletterCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterCampaignFactory extends Factory
{
    protected $model = NewsletterCampaign::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'sent']);
        return [
            'created_by_user_id' => User::factory(),
            'subject'            => fake()->sentence(6),
            'body_html'          => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'body_text'          => fake()->paragraphs(3, true),
            'status'             => $status,
            'scheduled_at'       => null,
            'sent_at'            => $status === 'sent' ? fake()->dateTimeBetween('-6 months', 'now') : null,
            'sent_count'         => $status === 'sent' ? fake()->numberBetween(100, 1000) : 0,
        ];
    }
}
```

Create `app/database/factories/ContactMessageFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'email'      => fake()->safeEmail(),
            'phone'      => fake()->optional(0.7)->phoneNumber(),
            'subject'    => fake()->sentence(4),
            'message'    => fake()->paragraph(),
            'ip_address' => fake()->ipv4(),
            'read'       => fake()->boolean(60),
        ];
    }
}
```

Create `app/database/factories/AdvertisingContactFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\AdvertisingContact;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertisingContactFactory extends Factory
{
    protected $model = AdvertisingContact::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email'     => fake()->safeEmail(),
            'message'   => fake()->paragraph(),
            'zone'      => fake()->randomElement(['home-top', 'sidebar', 'footer', 'events-page']),
            'read'      => fake()->boolean(50),
        ];
    }
}
```

- [ ] **Step 7: Correr**

```bash
docker compose exec app php artisan migrate:fresh --no-interaction
docker compose exec app php artisan tinker --execute="echo App\Models\Survey::factory()->has(App\Models\SurveyResponse::factory()->count(5), 'responses')->create()->responses->count() . ' responses; ' . App\Models\NewsletterSubscriber::factory()->count(10)->create()->count() . ' subs; ' . App\Models\ContactMessage::factory()->count(5)->create()->count() . ' messages';"
```

Expected: `5 responses; 10 subs; 5 messages`.

- [ ] **Step 8: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): surveys + newsletter + contact + advertising"
```

---

## Task 9: Demo seeder orquestador + migration rollback test

**Files:**
- Create: `app/database/seeders/DemoDataSeeder.php`
- Modify: `app/database/seeders/DatabaseSeeder.php`
- Create: `app/tests/Feature/Models/MigrationRollbackTest.php`

- [ ] **Step 1: DemoDataSeeder**

Create `app/database/seeders/DemoDataSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\{
    AdvertisingContact, Classified, ClassifiedCategory, ClassifiedContact,
    ContactMessage, Event, EventRegistration, GalleryImage, Lodging,
    Media, NearbyPlace, News, NewsCategory, NewsletterCampaign,
    NewsletterSubscriber, Page, Recipe, Rental, ServiceProvider,
    Survey, SurveyResponse, Tide, UsefulInfo, Venue,
};
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Taxonomías
        $newsCats       = NewsCategory::factory()->count(4)->create();
        $classifiedCats = ClassifiedCategory::factory()->count(6)->create();

        // Contenido editorial
        News::factory()->count(10)->recycle($newsCats)->create();
        Event::factory()->count(8)->create()->each(function ($event) {
            if ($event->accepts_registrations) {
                EventRegistration::factory()->count(5)->create(['event_id' => $event->id]);
            }
        });

        // Directorios
        Lodging::factory()->count(10)->create();
        Venue::factory()->count(8)->create();
        Rental::factory()->count(6)->create();
        Classified::factory()->count(10)->recycle($classifiedCats)->create()->each(function ($classified) {
            ClassifiedContact::factory()->count(fake()->numberBetween(0, 3))->create([
                'classified_id' => $classified->id,
            ]);
        });
        ServiceProvider::factory()->count(10)->create();
        Recipe::factory()->count(10)->create();
        GalleryImage::factory()->count(20)->create();
        NearbyPlace::factory()->count(6)->create();
        UsefulInfo::factory()->count(8)->create();
        Page::factory()->count(6)->create();

        // Mareas: 30 días consecutivos desde hoy
        $today = now();
        for ($i = 0; $i < 30; $i++) {
            Tide::factory()->create(['date' => $today->copy()->addDays($i)->format('Y-m-d')]);
        }

        // Engagement
        $survey = Survey::factory()->create();
        SurveyResponse::factory()->count(40)->create(['survey_id' => $survey->id]);
        NewsletterSubscriber::factory()->count(50)->create();
        NewsletterCampaign::factory()->count(5)->create();
        ContactMessage::factory()->count(15)->create();
        AdvertisingContact::factory()->count(8)->create();

        // Media demo: agregar 1-3 imágenes a cada contenido visual
        foreach (News::all() as $n) {
            Media::factory()->count(fake()->numberBetween(1, 3))->create([
                'mediable_id'   => $n->id,
                'mediable_type' => News::class,
            ]);
        }
        foreach (Lodging::all() as $l) {
            Media::factory()->count(fake()->numberBetween(2, 5))->create([
                'mediable_id'   => $l->id,
                'mediable_type' => Lodging::class,
            ]);
        }
        foreach (Venue::all() as $v) {
            Media::factory()->count(fake()->numberBetween(1, 4))->create([
                'mediable_id'   => $v->id,
                'mediable_type' => Venue::class,
            ]);
        }
        foreach (Classified::all() as $c) {
            Media::factory()->count(fake()->numberBetween(0, 3))->create([
                'mediable_id'   => $c->id,
                'mediable_type' => Classified::class,
            ]);
        }
    }
}
```

- [ ] **Step 2: Modificar DatabaseSeeder**

Edit `app/database/seeders/DatabaseSeeder.php` — reemplazar body del método `run` con:

```php
public function run(): void
{
    $this->call([
        RolesAndPermissionsSeeder::class,
        DemoUsersSeeder::class,
        DemoDataSeeder::class,
    ]);
}
```

- [ ] **Step 3: Test de rollback de migraciones**

Create `app/tests/Feature/Models/MigrationRollbackTest.php`:

```php
<?php

namespace Tests\Feature\Models;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationRollbackTest extends TestCase
{
    public function test_all_custom_tables_exist_after_migrate(): void
    {
        // migrate:fresh ya corrió antes del test via base setup o manual
        Artisan::call('migrate:fresh', ['--force' => true]);

        $expected = [
            'media',
            'news', 'news_categories',
            'events', 'event_registrations',
            'lodgings', 'venues', 'rentals',
            'classifieds', 'classified_categories', 'classified_contacts',
            'service_providers', 'recipes', 'gallery_images',
            'nearby_places', 'useful_info', 'pages', 'tides',
            'surveys', 'survey_responses',
            'newsletter_subscribers', 'newsletter_campaigns',
            'contact_messages', 'advertising_contacts',
        ];

        foreach ($expected as $table) {
            $this->assertTrue(Schema::hasTable($table), "Table {$table} should exist after migrate");
        }
    }

    public function test_rollback_drops_all_custom_tables_cleanly(): void
    {
        Artisan::call('migrate:fresh', ['--force' => true]);
        $exit = Artisan::call('migrate:rollback', ['--step' => 25, '--force' => true]);

        $this->assertSame(0, $exit, 'migrate:rollback should exit 0');

        $droppedCheck = ['news', 'events', 'lodgings', 'classifieds', 'media'];
        foreach ($droppedCheck as $table) {
            $this->assertFalse(
                Schema::hasTable($table),
                "Table {$table} should be dropped after rollback"
            );
        }
    }
}
```

- [ ] **Step 4: Correr el seed final + tests**

```bash
docker compose exec app php artisan migrate:fresh --seed --no-interaction
docker compose exec app php artisan test tests/Feature/Models/
```

Expected:
- migrate:fresh --seed: ejecuta sin error, data demo poblada
- Todos los tests de modelos pasan (MediaPolymorphic, News, Event, Lodging, MigrationRollback)

Verificar conteos:

```bash
docker compose exec app php artisan tinker --execute="
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'News: ' . App\Models\News::count() . PHP_EOL;
echo 'Events: ' . App\Models\Event::count() . PHP_EOL;
echo 'Lodgings: ' . App\Models\Lodging::count() . PHP_EOL;
echo 'Media: ' . App\Models\Media::count() . PHP_EOL;
echo 'Subscribers: ' . App\Models\NewsletterSubscriber::count() . PHP_EOL;
echo 'Tides: ' . App\Models\Tide::count() . PHP_EOL;
"
```

Expected approximate counts:
- Users: 3
- News: 10
- Events: 8
- Lodgings: 10
- Media: 50+
- Subscribers: 50
- Tides: 30

- [ ] **Step 5: Correr suite completa**

```bash
docker compose exec app php artisan test
```

Expected: todos los tests pasan (Fase 1 tests + nuevos = más de 60 tests).

- [ ] **Step 6: Commit**

```bash
cd /home/juan/sitios/balneario-el-condor
git add app/
git commit -m "feat(schema): DemoDataSeeder completo + test de rollback de migraciones"
```

---

## Criterios de aceptación de Fase 2

- [ ] 23 migraciones nuevas creadas (media + 22 tablas de dominio).
- [ ] 23 modelos Eloquent con `$fillable`, `$casts`, relaciones.
- [ ] 23 factories con Faker en español argentino produciendo datos realistas.
- [ ] Sistema `media` polimórfico funcionando (tests passing).
- [ ] `php artisan migrate:fresh --seed` ejecuta sin errores.
- [ ] DB resultante tiene: 3 users, ~10 registros por módulo de contenido, 30 mareas, 50 suscriptores newsletter.
- [ ] `php artisan test` corre verde (Fase 1 + Fase 2 = ~60+ tests).
- [ ] `php artisan migrate:rollback --step=25` revierte todas las tablas de esta fase sin errores.
- [ ] Ninguna migración rompe la integridad referencial (FKs válidas).
- [ ] Todos los modelos con coordenadas usan `decimal(10,7)`.
- [ ] Todos los modelos migrables tienen columna `legacy_id` nullable unique.

Con todo esto verde, se puede pasar al **Plan 3 — ETL** (Fase 3 del spec).
