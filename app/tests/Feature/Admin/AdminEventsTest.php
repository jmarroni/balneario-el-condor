<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Event;

class AdminEventsTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Event::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/events')->assertOk()->assertSee('Eventos');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/events')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/events', [
            'title'       => 'Fiesta de prueba',
            'description' => 'Descripción',
            'location'    => 'Playa',
            'starts_at'   => now()->format('Y-m-d\TH:i'),
            'ends_at'     => now()->addHours(3)->format('Y-m-d\TH:i'),
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('events', ['title' => 'Fiesta de prueba']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/events', [])
            ->assertSessionHasErrors(['title']);
    }

    public function test_admin_can_update(): void
    {
        $event = Event::factory()->create();
        $this->asAdmin()->put("/admin/events/{$event->id}", [
            'title' => 'Nuevo título',
        ])->assertRedirect();
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Nuevo título']);
    }

    public function test_admin_can_delete(): void
    {
        $event = Event::factory()->create();
        $this->asAdmin()->delete("/admin/events/{$event->id}")->assertRedirect();
        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $event = Event::factory()->create();
        $this->asModerator()->delete("/admin/events/{$event->id}")->assertForbidden();
    }
}
