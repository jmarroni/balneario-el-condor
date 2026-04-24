<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\EventRegistration;

class AdminEventRegistrationsTest extends AdminTestCase
{
    public function test_admin_sees_index_for_event(): void
    {
        $event = Event::factory()->create();
        EventRegistration::factory()->count(3)->create(['event_id' => $event->id]);
        EventRegistration::factory()->count(2)->create(); // other event

        $response = $this->asAdmin()->get("/admin/events/{$event->id}/registrations");
        $response->assertOk()->assertSee('Inscripciones');
    }

    public function test_moderator_can_see_index(): void
    {
        $event = Event::factory()->create();
        EventRegistration::factory()->count(2)->create(['event_id' => $event->id]);

        $this->asModerator()->get("/admin/events/{$event->id}/registrations")->assertOk();
    }

    public function test_admin_sees_show(): void
    {
        $registration = EventRegistration::factory()->create([
            'extra_data' => ['custom_field' => 'valor'],
        ]);
        $this->asAdmin()->get("/admin/registrations/{$registration->id}")
            ->assertOk()
            ->assertSee($registration->name)
            ->assertSee('custom_field');
    }

    public function test_admin_can_delete(): void
    {
        $registration = EventRegistration::factory()->create();
        $this->asAdmin()->delete("/admin/registrations/{$registration->id}")->assertRedirect();
        $this->assertDatabaseMissing('event_registrations', ['id' => $registration->id]);
    }

    public function test_moderator_can_delete(): void
    {
        $registration = EventRegistration::factory()->create();
        $this->asModerator()->delete("/admin/registrations/{$registration->id}")->assertRedirect();
        $this->assertDatabaseMissing('event_registrations', ['id' => $registration->id]);
    }

    public function test_editor_can_delete(): void
    {
        $registration = EventRegistration::factory()->create();
        $this->asEditor()->delete("/admin/registrations/{$registration->id}")->assertRedirect();
        $this->assertDatabaseMissing('event_registrations', ['id' => $registration->id]);
    }
}
