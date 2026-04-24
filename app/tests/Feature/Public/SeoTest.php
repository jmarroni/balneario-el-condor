<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Event;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_sitemap_returns_xml_with_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $body = $response->getContent();
        $this->assertStringContainsString('<?xml version="1.0" encoding="UTF-8"?>', $body);
        $this->assertStringContainsString('<urlset', $body);
        $this->assertStringContainsString(route('home'), $body);
        $this->assertStringContainsString(route('novedades.index'), $body);
        $this->assertStringContainsString(route('eventos.index'), $body);
        $this->assertStringContainsString(route('mareas.index'), $body);
    }

    public function test_sitemap_includes_dynamic_news(): void
    {
        Cache::flush();

        $news = News::factory()->create([
            'slug'         => 'una-novedad-publicada',
            'published_at' => now()->subDay(),
        ]);

        $body = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString(route('novedades.show', $news), $body);
    }

    public function test_sitemap_excludes_draft_news(): void
    {
        Cache::flush();

        $draft = News::factory()->create([
            'slug'         => 'borrador-no-publicado',
            'published_at' => null,
        ]);

        $body = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringNotContainsString(route('novedades.show', $draft), $body);
    }

    public function test_robots_txt_is_served(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $body = $response->getContent();

        $this->assertStringContainsString('User-agent: *', $body);
        $this->assertStringContainsString('Disallow: /admin', $body);
        $this->assertStringContainsString('Sitemap:', $body);
    }

    public function test_home_has_jsonld(): void
    {
        $body = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('application/ld+json', $body);
        $this->assertStringContainsString('TouristDestination', $body);
        $this->assertStringContainsString('Balneario El Cóndor', $body);
    }

    public function test_event_show_has_jsonld(): void
    {
        $event = Event::factory()->create([
            'slug'      => 'fiesta-de-prueba',
            'title'     => 'Fiesta de Prueba',
            'starts_at' => now()->addDays(5),
        ]);

        $body = $this->get(route('eventos.show', $event))->assertOk()->getContent();

        $this->assertStringContainsString('application/ld+json', $body);
        $this->assertStringContainsString('"@type":"Event"', $body);
        $this->assertStringContainsString('Fiesta de Prueba', $body);
    }
}
