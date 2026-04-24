<?php

namespace Tests\Feature\Models;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_event_has_many_registrations(): void
    {
        $event = Event::factory()->create();
        EventRegistration::factory()->count(3)->create(['event_id' => $event->id]);

        $this->assertCount(3, $event->registrations);
    }

    public function test_registration_extra_data_is_array_cast(): void
    {
        $reg = EventRegistration::factory()->create();
        $this->assertIsArray($reg->extra_data);
    }

    public function test_cascade_delete_removes_registrations(): void
    {
        $event = Event::factory()->create();
        EventRegistration::factory()->count(2)->create(['event_id' => $event->id]);

        $event->forceDelete();

        $this->assertDatabaseCount('event_registrations', 0);
    }

    public function test_event_soft_delete(): void
    {
        $event = Event::factory()->create();
        $event->delete();
        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }
}
