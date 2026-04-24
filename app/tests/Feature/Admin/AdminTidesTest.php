<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Tide;

class AdminTidesTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Tide::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/tides')->assertOk()->assertSee('Mareas');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/tides')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/tides', [
            'location'   => 'El Cóndor',
            'date'       => '2026-05-15',
            'first_high' => '08:30',
            'first_low'  => '14:45',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('tides', ['date' => '2026-05-15', 'location' => 'El Cóndor']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/tides', [])
            ->assertSessionHasErrors(['date']);
    }

    public function test_admin_can_update(): void
    {
        $tide = Tide::factory()->create();
        $this->asAdmin()->put("/admin/tides/{$tide->id}", [
            'date'              => $tide->date->format('Y-m-d'),
            'location'          => $tide->location,
            'first_high_height' => '3.50 m',
        ])->assertRedirect();
        $this->assertDatabaseHas('tides', ['id' => $tide->id, 'first_high_height' => '3.50 m']);
    }

    public function test_admin_can_delete(): void
    {
        $tide = Tide::factory()->create();
        $this->asAdmin()->delete("/admin/tides/{$tide->id}")->assertRedirect();
        $this->assertDatabaseMissing('tides', ['id' => $tide->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $tide = Tide::factory()->create();
        $this->asModerator()->delete("/admin/tides/{$tide->id}")->assertForbidden();
    }
}
