<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Tide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MareasTest extends TestCase
{
    use RefreshDatabase;

    public function test_mareas_index_shows_today_by_default(): void
    {
        Tide::factory()->create([
            'date'              => today(),
            'first_high'        => '08:42:00',
            'first_high_height' => '3.85 m',
        ]);

        $this->get('/mareas')
            ->assertOk()
            ->assertSee('Mareas')
            ->assertSee('08:42')
            ->assertSee('3.85');
    }

    public function test_mareas_navigates_by_date_query(): void
    {
        $target = now()->addDays(10)->startOfDay();

        Tide::factory()->create([
            'date'              => $target->toDateString(),
            'first_high'        => '11:15:00',
            'first_high_height' => '4.10 m',
        ]);

        $this->get('/mareas?fecha='.$target->toDateString())
            ->assertOk()
            ->assertSee('11:15')
            ->assertSee('4.10');
    }

    public function test_mareas_shows_week_table(): void
    {
        // Plantamos un día dentro de la semana actual para chequear que la tabla
        // renderiza encabezados y al menos esa fila con horarios.
        $monday = now()->startOfWeek();

        Tide::factory()->create([
            'date'              => $monday->toDateString(),
            'first_high'        => '06:30:00',
            'first_high_height' => '3.20 m',
        ]);

        $response = $this->get('/mareas')->assertOk();

        // Encabezados de la tabla — el carácter "ª" se escapa a entidad HTML por Blade.
        $response->assertSeeText('1.ª Pleamar');
        $response->assertSeeText('1.ª Bajamar');
        $response->assertSeeText('2.ª Pleamar');
        $response->assertSeeText('2.ª Bajamar');
        $response->assertSee('06:30');
    }

    public function test_mareas_handles_missing_data_gracefully(): void
    {
        // Sin ningún registro en la tabla tides — la página debe responder 200,
        // mostrar el mensaje de "sin predicción" y links de navegación.
        $this->get('/mareas')
            ->assertOk()
            ->assertSee('Mareas')
            ->assertSee('No hay predicción cargada');
    }
}
