<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Media;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_published_news(): void
    {
        News::factory()->create(['title' => 'Apertura de temporada', 'published_at' => now()->subDay()]);
        News::factory()->create(['title' => 'Nueva pasarela en la playa', 'published_at' => now()->subHours(2)]);
        News::factory()->create(['title' => 'Concurso de fotografía', 'published_at' => now()->subWeek()]);

        $this->get('/novedades')
            ->assertOk()
            ->assertSee('Apertura de temporada')
            ->assertSee('Nueva pasarela en la playa')
            ->assertSee('Concurso de fotografía');
    }

    public function test_index_hides_draft_and_scheduled_news(): void
    {
        News::factory()->create(['title' => 'Borrador secreto', 'published_at' => null]);
        News::factory()->create(['title' => 'Programada futura', 'published_at' => now()->addWeek()]);
        News::factory()->create(['title' => 'Publicada visible', 'published_at' => now()->subHour()]);

        $response = $this->get('/novedades')->assertOk();

        $response->assertDontSee('Borrador secreto');
        $response->assertDontSee('Programada futura');
        $response->assertSee('Publicada visible');
    }

    public function test_index_filters_by_category(): void
    {
        $turismo = NewsCategory::factory()->create(['name' => 'Turismo', 'slug' => 'turismo']);
        $pesca = NewsCategory::factory()->create(['name' => 'Pesca', 'slug' => 'pesca']);

        News::factory()->create([
            'title' => 'Crónica turística del verano',
            'news_category_id' => $turismo->id,
            'published_at' => now()->subDay(),
        ]);
        News::factory()->create([
            'title' => 'Récord de pejerreyes en la ría',
            'news_category_id' => $pesca->id,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/novedades?categoria=pesca')->assertOk();

        $response->assertSee('Récord de pejerreyes en la ría');
        $response->assertDontSee('Crónica turística del verano');
    }

    public function test_show_renders_article_with_body_and_media(): void
    {
        $news = News::factory()->create([
            'title' => 'La marea del cambio',
            'slug' => 'la-marea-del-cambio',
            'body' => "Primer parrafo del articulo del balneario.\n\nSegundo parrafo con mas contenido.",
            'published_at' => now()->subHour(),
        ]);

        Media::factory()->create([
            'mediable_type' => News::class,
            'mediable_id' => $news->id,
            'path' => 'novedades/marea.jpg',
            'sort_order' => 0,
        ]);

        $this->get('/novedades/la-marea-del-cambio')
            ->assertOk()
            ->assertSee('La marea del cambio')
            ->assertSee('Primer parrafo del articulo del balneario.')
            ->assertSee('Segundo parrafo con mas contenido.');
    }

    public function test_show_returns_404_for_unpublished_news(): void
    {
        $draft = News::factory()->create([
            'slug' => 'borrador-no-publicado',
            'published_at' => null,
        ]);

        $this->get('/novedades/borrador-no-publicado')->assertNotFound();

        $future = News::factory()->create([
            'slug' => 'programada-futura',
            'published_at' => now()->addDays(3),
        ]);

        $this->get('/novedades/programada-futura')->assertNotFound();
    }

    public function test_show_increments_views_counter(): void
    {
        $news = News::factory()->create([
            'slug' => 'contador-vistas',
            'views' => 5,
            'published_at' => now()->subHour(),
        ]);

        $this->get('/novedades/contador-vistas')->assertOk();

        $this->assertSame(6, $news->fresh()->views);
    }

    public function test_show_lists_related_news_in_same_category(): void
    {
        $turismo = NewsCategory::factory()->create(['slug' => 'turismo']);
        $pesca = NewsCategory::factory()->create(['slug' => 'pesca']);

        $main = News::factory()->create([
            'title' => 'Nota principal',
            'slug' => 'nota-principal',
            'news_category_id' => $turismo->id,
            'published_at' => now()->subDay(),
        ]);

        $relatedA = News::factory()->create([
            'title' => 'Hermana turistica A',
            'news_category_id' => $turismo->id,
            'published_at' => now()->subDays(2),
        ]);
        $relatedB = News::factory()->create([
            'title' => 'Hermana turistica B',
            'news_category_id' => $turismo->id,
            'published_at' => now()->subDays(3),
        ]);

        $other = News::factory()->create([
            'title' => 'De otra categoria',
            'news_category_id' => $pesca->id,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->get('/novedades/nota-principal')->assertOk();

        $response->assertSee('Hermana turistica A');
        $response->assertSee('Hermana turistica B');
        $response->assertDontSee('De otra categoria');
    }
}
