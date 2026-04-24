<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Event;
use App\Models\News;
use App\Models\Tide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_hero_heading(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('El faro,')
            ->assertSee('el cóndor');
    }

    public function test_home_shows_featured_news_when_exists(): void
    {
        News::factory()->create([
            'title'        => 'Fiesta del Tejo',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Fiesta del Tejo');
    }

    public function test_home_shows_upcoming_events(): void
    {
        Event::factory()->create([
            'title'     => 'Peña Patagónica',
            'starts_at' => now()->addDays(5),
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Peña Patagónica');
    }

    public function test_home_shows_today_tide(): void
    {
        Tide::factory()->create([
            'date'              => today(),
            'first_high'        => '08:42:00',
            'first_high_height' => '3.85 m',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('08:42')
            ->assertSee('3.85');
    }

    public function test_home_empty_state_shows_gracefully(): void
    {
        // Sin factories — la home debe renderizar 200 con todos los empty states.
        $this->get('/')
            ->assertOk()
            ->assertSee('Pronto más novedades')
            ->assertSee('Agenda en construcción');
    }
}
