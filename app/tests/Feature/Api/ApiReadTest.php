<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Classified;
use App\Models\ClassifiedCategory;
use App\Models\Event;
use App\Models\GalleryImage;
use App\Models\Lodging;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Tide;
use App\Models\User;
use App\Models\Venue;
use Carbon\CarbonImmutable;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiReadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function userWithRole(string $role): array
    {
        $user  = User::factory()->create();
        $user->assignRole($role);
        $token = $user->createToken('test')->plainTextToken;

        return [$user, $token];
    }

    private function authHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_unauth_returns_401(): void
    {
        $this->getJson('/api/v1/news')->assertStatus(401);
    }

    public function test_list_news_paginated_with_envelope(): void
    {
        [, $token] = $this->userWithRole('editor');

        $cat = NewsCategory::factory()->create();
        News::factory()->count(3)->create([
            'news_category_id' => $cat->id,
            'published_at'     => now()->subDay(),
        ]);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/news?per_page=2');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'title', 'slug', 'body', 'excerpt', 'reading_minutes', 'published_at', 'links' => ['self']],
                ],
                'meta' => ['total', 'per_page', 'current_page', 'last_page'],
            ])
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3);
    }

    public function test_show_news_returns_full_resource(): void
    {
        [, $token] = $this->userWithRole('editor');

        $cat  = NewsCategory::factory()->create();
        $news = News::factory()->create([
            'news_category_id' => $cat->id,
            'published_at'     => now()->subDay(),
        ]);

        $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/news/'.$news->slug)
            ->assertOk()
            ->assertJsonPath('data.slug', $news->slug)
            ->assertJsonPath('data.id', $news->id)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'slug', 'body', 'category', 'links' => ['self']],
                'meta' => ['version', 'generated_at'],
            ]);
    }

    public function test_404_for_unknown_slug(): void
    {
        [, $token] = $this->userWithRole('editor');

        $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/news/no-existe-este-slug-xyz')
            ->assertStatus(404);
    }

    public function test_events_filter_proximos_by_default(): void
    {
        [, $token] = $this->userWithRole('editor');

        Event::factory()->create(['starts_at' => now()->subWeek(), 'title' => 'Pasado']);
        Event::factory()->create(['starts_at' => now()->addWeek(), 'title' => 'Proximo']);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/events');

        $response->assertOk()
            ->assertJsonPath('meta.cuando', 'proximos')
            ->assertJsonPath('meta.total', 1);

        $titles = collect($response->json('data'))->pluck('title')->all();
        $this->assertContains('Proximo', $titles);
        $this->assertNotContains('Pasado', $titles);
    }

    public function test_lodgings_filter_by_type(): void
    {
        [, $token] = $this->userWithRole('editor');

        Lodging::factory()->create(['type' => 'hotel', 'name' => 'Test Hotel A']);
        Lodging::factory()->create(['type' => 'casa', 'name' => 'Test Casa B']);
        Lodging::factory()->create(['type' => 'camping', 'name' => 'Test Camping C']);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/lodgings?type=hotel');

        $response->assertOk()->assertJsonPath('meta.total', 1);

        $names = collect($response->json('data'))->pluck('name')->all();
        $this->assertContains('Test Hotel A', $names);
    }

    public function test_venues_filter_by_category(): void
    {
        [, $token] = $this->userWithRole('editor');

        Venue::factory()->create(['category' => 'gourmet', 'name' => 'V Gourmet']);
        Venue::factory()->create(['category' => 'nightlife', 'name' => 'V Night']);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/venues?category=gourmet');

        $response->assertOk()->assertJsonPath('meta.total', 1);
        $this->assertSame('V Gourmet', $response->json('data.0.name'));
    }

    public function test_classifieds_filter_by_category(): void
    {
        [, $token] = $this->userWithRole('editor');

        $catA = ClassifiedCategory::factory()->create(['slug' => 'inmuebles-test']);
        $catB = ClassifiedCategory::factory()->create(['slug' => 'vehiculos-test']);

        Classified::factory()->create(['classified_category_id' => $catA->id, 'title' => 'Casa Test']);
        Classified::factory()->create(['classified_category_id' => $catB->id, 'title' => 'Auto Test']);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/classifieds?categoria=inmuebles-test');

        $response->assertOk()->assertJsonPath('meta.total', 1);
        $this->assertSame('Casa Test', $response->json('data.0.title'));
    }

    public function test_gallery_filter_by_year(): void
    {
        [, $token] = $this->userWithRole('editor');

        GalleryImage::factory()->create(['taken_on' => '2020-05-10', 'title' => 'Vieja 2020']);
        GalleryImage::factory()->create(['taken_on' => '2024-08-15', 'title' => 'Reciente 2024']);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/gallery?year=2024');

        $response->assertOk()->assertJsonPath('meta.total', 1);
        $this->assertSame('Reciente 2024', $response->json('data.0.title'));
    }

    public function test_tides_today_default(): void
    {
        [, $token] = $this->userWithRole('editor');

        $today = CarbonImmutable::today()->toDateString();
        Tide::factory()->create(['date' => $today, 'first_high' => '10:00:00']);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/tides');

        $response->assertOk()
            ->assertJsonPath('meta.date', $today)
            ->assertJsonPath('data.first_high', '10:00:00');
    }

    public function test_tides_week_returns_seven_days(): void
    {
        [, $token] = $this->userWithRole('editor');

        // Fixed Wed 2024-08-14 → Mon 2024-08-12 to Sun 2024-08-18
        $reference = '2024-08-14';
        for ($i = 0; $i < 7; $i++) {
            $d = CarbonImmutable::parse('2024-08-12')->addDays($i)->toDateString();
            Tide::factory()->create(['date' => $d, 'location' => 'El Cóndor']);
        }
        // Out-of-range marker (next Monday)
        Tide::factory()->create(['date' => '2024-08-19', 'location' => 'El Cóndor']);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/tides/week?date='.$reference);

        $response->assertOk()
            ->assertJsonPath('meta.week_start', '2024-08-12')
            ->assertJsonPath('meta.week_end', '2024-08-18')
            ->assertJsonPath('meta.count', 7);

        $this->assertCount(7, $response->json('data'));
    }

    public function test_weather_reads_cache(): void
    {
        [, $token] = $this->userWithRole('editor');

        Cache::put('weather:current', [
            'temp_c'      => 22,
            'description' => 'Soleado',
        ], 60);

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/weather');

        $response->assertOk()
            ->assertJsonPath('data.temp_c', 22)
            ->assertJsonPath('data.description', 'Soleado')
            ->assertJsonPath('meta.source', 'cache');
    }

    public function test_weather_returns_null_when_no_cache(): void
    {
        [, $token] = $this->userWithRole('editor');

        Cache::forget('weather:current');

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/weather');

        $response->assertOk()
            ->assertJsonPath('data', null)
            ->assertJsonPath('meta.message', 'No disponible');
    }

    public function test_moderator_forbidden_on_non_moderable(): void
    {
        [, $token] = $this->userWithRole('moderator');

        $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/news')
            ->assertStatus(403);

        $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/lodgings')
            ->assertStatus(403);
    }

    public function test_pagination_per_page_parameter(): void
    {
        [, $token] = $this->userWithRole('editor');

        Lodging::factory()->count(8)->create();

        $response = $this->withHeaders($this->authHeaders($token))
            ->getJson('/api/v1/lodgings?per_page=3');

        $response->assertOk()
            ->assertJsonPath('meta.per_page', 3)
            ->assertJsonPath('meta.total', 8)
            ->assertJsonPath('meta.last_page', 3);

        $this->assertCount(3, $response->json('data'));
    }
}
