<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_shows_upcoming_by_default(): void
    {
        Event::factory()->create([
            'title' => 'Peña patagónica de verano',
            'starts_at' => now()->addDays(5),
            'accepts_registrations' => false,
            'featured' => false,
        ]);
        Event::factory()->create([
            'title' => 'Edicion antigua del festival',
            'starts_at' => now()->subYear(),
            'accepts_registrations' => false,
            'featured' => false,
        ]);

        $response = $this->get('/eventos')->assertOk();

        $response->assertSee('Peña patagónica de verano');
        $response->assertDontSee('Edicion antigua del festival');
    }

    public function test_index_cuando_pasados_shows_past_events(): void
    {
        Event::factory()->create([
            'title' => 'Encuentro futuro',
            'starts_at' => now()->addDays(10),
            'accepts_registrations' => false,
        ]);
        Event::factory()->create([
            'title' => 'Encuentro pasado memorable',
            'starts_at' => now()->subMonths(2),
            'accepts_registrations' => false,
        ]);

        $response = $this->get('/eventos?cuando=pasados')->assertOk();

        $response->assertSee('Encuentro pasado memorable');
        $response->assertDontSee('Encuentro futuro');
    }

    public function test_show_renders_event(): void
    {
        $event = Event::factory()->create([
            'title' => 'Festival de la Costa',
            'slug' => 'festival-de-la-costa',
            'description' => 'Tres días de música a orillas del río Negro.',
            'location' => 'Costanera',
            'starts_at' => now()->addDays(20),
            'accepts_registrations' => false,
        ]);

        $this->get(route('eventos.show', $event))
            ->assertOk()
            ->assertSee('Festival de la Costa')
            ->assertSee('Tres días de música a orillas del río Negro.')
            ->assertSee('Costanera');
    }

    public function test_show_404_for_nonexistent_slug(): void
    {
        $this->get('/eventos/no-existe-este-evento')->assertNotFound();
    }

    public function test_register_rejects_when_not_accepting(): void
    {
        $event = Event::factory()->create([
            'slug' => 'evento-cerrado',
            'accepts_registrations' => false,
        ]);

        $this->post(route('eventos.register', $event), [
            'name' => 'Visitante',
            'email' => 'v@example.com',
        ])->assertForbidden();

        $this->assertDatabaseCount('event_registrations', 0);
    }

    public function test_register_persists_registration_with_extra_data(): void
    {
        $event = Event::factory()->create([
            'slug' => 'evento-abierto',
            'accepts_registrations' => true,
        ]);

        $response = $this->post(route('eventos.register', $event), [
            'name' => 'Ana Pereyra',
            'email' => 'ana@example.com',
            'phone' => '+54 9 2920 11 22 33',
        ]);

        $response->assertRedirect(route('eventos.show', $event));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'name' => 'Ana Pereyra',
            'email' => 'ana@example.com',
            'phone' => '+54 9 2920 11 22 33',
        ]);

        $registration = EventRegistration::where('event_id', $event->id)->first();
        $this->assertNotNull($registration);
        $this->assertStringStartsWith('form-', (string) $registration->legacy_id);
    }

    public function test_register_validates_required_fields(): void
    {
        $event = Event::factory()->create([
            'slug' => 'evento-validable',
            'accepts_registrations' => true,
        ]);

        $this->post(route('eventos.register', $event), [])
            ->assertSessionHasErrors(['name', 'email']);

        $this->assertDatabaseCount('event_registrations', 0);
    }

    public function test_register_tejo_saves_custom_fields(): void
    {
        $event = Event::factory()->create([
            'title' => 'Fiesta del Tejo',
            'slug' => 'fiesta-del-tejo',
            'accepts_registrations' => true,
        ]);

        $response = $this->post(route('eventos.register', $event), [
            'name' => 'Carlos del Tejo',
            'email' => 'carlos@example.com',
            'phone' => '+54 11 2222 3333',
            'club_asociacion' => 'Club Atlético Río Negro',
            'provincia' => 'Río Negro',
            'localidad' => 'Viedma',
            'alojamiento' => '1',
            'concursantes' => 4,
            'entradas' => 6,
            'excursiones' => 2,
            'cena' => 4,
            'comentarios' => 'Llegamos el viernes a la mañana.',
        ]);

        $response->assertRedirect(route('eventos.show', $event));

        $registration = EventRegistration::where('event_id', $event->id)->first();
        $this->assertNotNull($registration);
        $this->assertSame('Carlos del Tejo', $registration->name);
        $this->assertSame('carlos@example.com', $registration->email);

        $extra = $registration->extra_data;
        $this->assertIsArray($extra);
        $this->assertSame('Club Atlético Río Negro', $extra['club_asociacion']);
        $this->assertSame('Río Negro', $extra['provincia']);
        $this->assertSame('Viedma', $extra['localidad']);
        $this->assertSame(4, $extra['concursantes']);
        $this->assertSame(6, $extra['entradas']);
        $this->assertTrue((bool) $extra['alojamiento']);
        $this->assertSame('Llegamos el viernes a la mañana.', $extra['comentarios']);
    }
}
