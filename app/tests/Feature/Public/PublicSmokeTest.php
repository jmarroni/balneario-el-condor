<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Classified;
use App\Models\Event;
use App\Models\Lodging;
use App\Models\NearbyPlace;
use App\Models\News;
use App\Models\Page;
use App\Models\Recipe;
use App\Models\Rental;
use App\Models\ServiceProvider;
use App\Models\Venue;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke E2E del sitio público (Fase 5 / Task 10).
 *
 * Confirma que todas las rutas públicas (índices, sitemap, robots, forms)
 * y las páginas show de contenido seedeado responden 200.
 */
class PublicSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_index_routes_respond_200(): void
    {
        $this->seed(DemoDataSeeder::class);

        $routes = [
            '/', '/novedades', '/eventos', '/hospedajes', '/gastronomia',
            '/alquileres', '/clasificados', '/galeria', '/mareas', '/clima',
            '/servicios', '/cercanos', '/informacion-util',
            '/contacto', '/newsletter', '/publicite',
            '/sitemap.xml', '/robots.txt',
        ];

        foreach ($routes as $r) {
            $this->get($r)->assertStatus(200, "Route {$r} failed");
        }
    }

    public function test_show_pages_render_for_seeded_records(): void
    {
        $this->seed(DemoDataSeeder::class);

        $news       = News::whereNotNull('published_at')->first();
        $event      = Event::first();
        $lodging    = Lodging::first();
        $venue      = Venue::first();
        $rental     = Rental::first();
        $classified = Classified::first();
        $recipe     = Recipe::first();
        $sp         = ServiceProvider::first();
        $nearby     = NearbyPlace::first();
        $page       = Page::where('published', true)->first();

        if ($news)       $this->get(route('novedades.show', $news))->assertOk();
        if ($event)      $this->get(route('eventos.show', $event))->assertOk();
        if ($lodging)    $this->get(route('hospedajes.show', $lodging))->assertOk();
        if ($venue)      $this->get(route('gastronomia.show', $venue))->assertOk();
        if ($rental)     $this->get(route('alquileres.show', $rental))->assertOk();
        if ($classified) $this->get(route('clasificados.show', $classified))->assertOk();
        if ($recipe)     $this->get(route('recetas.show', $recipe))->assertOk();
        if ($sp)         $this->get(route('servicios.show', $sp))->assertOk();
        if ($nearby)     $this->get(route('cercanos.show', $nearby))->assertOk();
        if ($page)       $this->get(route('pages.show', $page))->assertOk();
    }

    public function test_honeypot_applies_to_all_forms(): void
    {
        $payloads = [
            '/contacto' => [
                'name'             => 'Bot',
                'email'            => 'bot@example.test',
                'message'          => 'spammy spammy spam content from bots',
                'captcha_honeypot' => 'spam',
            ],
            '/newsletter' => [
                'email'            => 'bot@example.test',
                'captcha_honeypot' => 'spam',
            ],
            '/publicite' => [
                'name'             => 'Bot',
                'last_name'        => 'Spammer',
                'email'            => 'bot@example.test',
                'message'          => 'spammy spammy spam content from bots',
                'zone'             => 'sidebar',
                'captcha_honeypot' => 'spam',
            ],
        ];

        foreach ($payloads as $url => $body) {
            $this->from($url)
                ->post($url, $body)
                ->assertSessionHasErrors('captcha_honeypot');
        }
    }

    public function test_404_for_unpublished_content(): void
    {
        $news = News::factory()->create(['published_at' => null]);
        $this->get(route('novedades.show', $news))->assertNotFound();

        $page = Page::factory()->create(['published' => false]);
        $this->get(route('pages.show', $page))->assertNotFound();
    }
}
