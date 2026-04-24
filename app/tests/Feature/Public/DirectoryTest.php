<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Lodging;
use App\Models\NearbyPlace;
use App\Models\Rental;
use App\Models\ServiceProvider;
use App\Models\UsefulInfo;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_hospedajes_index_shows_items(): void
    {
        Lodging::factory()->create([
            'name' => 'Hotel Faro Costanero',
            'slug' => 'hotel-faro-costanero',
            'type' => 'hotel',
        ]);

        $this->get('/hospedajes')
            ->assertOk()
            ->assertSee('Hospedajes')
            ->assertSee('Hotel Faro Costanero');
    }

    public function test_hospedajes_filter_by_type(): void
    {
        Lodging::factory()->create([
            'name' => 'Cabañas del Mar Solar',
            'slug' => 'cabanas-del-mar-solar',
            'type' => 'casa',
        ]);
        Lodging::factory()->create([
            'name' => 'Hotel Patagonia Centro',
            'slug' => 'hotel-patagonia-centro',
            'type' => 'hotel',
        ]);

        $response = $this->get('/hospedajes?tipo=hotel')->assertOk();
        $response->assertSee('Hotel Patagonia Centro');
        $response->assertDontSee('Cabañas del Mar Solar');

        $response2 = $this->get('/hospedajes?tipo=casa')->assertOk();
        $response2->assertSee('Cabañas del Mar Solar');
        $response2->assertDontSee('Hotel Patagonia Centro');
    }

    public function test_hospedajes_show_renders(): void
    {
        $lodging = Lodging::factory()->create([
            'name'        => 'Posada Vista Bahía Test',
            'slug'        => 'posada-vista-bahia-test',
            'description' => 'Una posada con vista directa a la desembocadura del río Negro.',
            'phone'       => '+54 9 2920 444555',
            'address'     => 'Costanera 1500, El Cóndor',
            'latitude'    => -41.05,
            'longitude'   => -62.85,
        ]);

        $this->get(route('hospedajes.show', $lodging))
            ->assertOk()
            ->assertSee('Posada Vista Bahía Test')
            ->assertSee('Una posada con vista directa')
            ->assertSee('+54 9 2920 444555')
            ->assertSee('Costanera 1500, El Cóndor');
    }

    public function test_venues_toggle_category(): void
    {
        Venue::factory()->create([
            'name'     => 'La Cocina del Faro Test',
            'slug'     => 'la-cocina-del-faro-test',
            'category' => 'gourmet',
        ]);
        Venue::factory()->create([
            'name'     => 'Bar Sunset Lounge Test',
            'slug'     => 'bar-sunset-lounge-test',
            'category' => 'nightlife',
        ]);

        // Default → gourmet
        $r1 = $this->get('/gastronomia')->assertOk();
        $r1->assertSee('La Cocina del Faro Test');
        $r1->assertDontSee('Bar Sunset Lounge Test');

        // Toggle → nightlife
        $r2 = $this->get('/gastronomia?categoria=nightlife')->assertOk();
        $r2->assertSee('Bar Sunset Lounge Test');
        $r2->assertDontSee('La Cocina del Faro Test');
    }

    public function test_rentals_index(): void
    {
        Rental::factory()->create([
            'title' => 'Casa Marina Tres Ambientes',
            'slug'  => 'casa-marina-tres-ambientes',
            'places' => 6,
        ]);

        $response = $this->get('/alquileres')->assertOk();
        $response->assertSee('Alquileres');
        $response->assertSee('Casa Marina Tres Ambientes');
    }

    public function test_service_providers_show(): void
    {
        $provider = ServiceProvider::factory()->create([
            'name'         => 'Plomería Hernández Costa',
            'slug'         => 'plomeria-hernandez-costa',
            'description'  => 'Servicio de plomería las 24 horas en El Cóndor.',
            'phone'        => '+54 9 2920 778899',
            'contact_name' => 'Juan Hernández',
        ]);

        $this->get(route('servicios.show', $provider))
            ->assertOk()
            ->assertSee('Plomería Hernández Costa')
            ->assertSee('Servicio de plomería las 24 horas')
            ->assertSee('+54 9 2920 778899')
            ->assertSee('Juan Hernández');
    }

    public function test_nearby_places_index(): void
    {
        NearbyPlace::factory()->create([
            'title' => 'Bahía Creek de Prueba',
            'slug'  => 'bahia-creek-de-prueba',
        ]);

        $this->get('/cercanos')
            ->assertOk()
            ->assertSee('Excursiones a un paso')
            ->assertSee('Bahía Creek de Prueba');
    }

    public function test_useful_info_index_shows_phones(): void
    {
        UsefulInfo::factory()->create([
            'title' => 'Bomberos El Cóndor',
            'phone' => '100 / 2920 555111',
        ]);
        UsefulInfo::factory()->create([
            'title' => 'Hospital Provincial Test',
            'phone' => '107',
        ]);

        $response = $this->get('/informacion-util')->assertOk();
        $response->assertSee('Información útil');
        $response->assertSee('Bomberos El Cóndor');
        $response->assertSee('100 / 2920 555111');
        $response->assertSee('Hospital Provincial Test');
        $response->assertSee('107');
    }
}
