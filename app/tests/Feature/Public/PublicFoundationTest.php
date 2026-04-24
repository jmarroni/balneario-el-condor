<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_returns_ok(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_home_includes_nav_links(): void
    {
        $this->get('/')
            ->assertSee('Novedades')
            ->assertSee('Eventos')
            ->assertSee('El Cóndor');
    }

    public function test_home_includes_footer_contact(): void
    {
        $this->get('/')
            ->assertSee('Turismo Municipal')
            ->assertSee('+54 9 2920');
    }

    public function test_home_loads_fonts(): void
    {
        $this->get('/')
            ->assertSee('Fraunces', false)
            ->assertSee('Instrument+Sans', false);
    }
}
