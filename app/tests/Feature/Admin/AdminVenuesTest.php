<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Venue;

class AdminVenuesTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Venue::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/venues')->assertOk()->assertSee('Locales');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/venues')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/venues', [
            'name'     => 'Restaurante Nuevo',
            'category' => 'gourmet',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('venues', ['name' => 'Restaurante Nuevo', 'category' => 'gourmet']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/venues', [])
            ->assertSessionHasErrors(['name', 'category']);
    }

    public function test_admin_can_update(): void
    {
        $venue = Venue::factory()->create();
        $this->asAdmin()->put("/admin/venues/{$venue->id}", [
            'name'     => 'Nombre actualizado',
            'category' => $venue->category,
        ])->assertRedirect();
        $this->assertDatabaseHas('venues', ['id' => $venue->id, 'name' => 'Nombre actualizado']);
    }

    public function test_admin_can_delete(): void
    {
        $venue = Venue::factory()->create();
        $this->asAdmin()->delete("/admin/venues/{$venue->id}")->assertRedirect();
        $this->assertSoftDeleted('venues', ['id' => $venue->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $venue = Venue::factory()->create();
        $this->asModerator()->delete("/admin/venues/{$venue->id}")->assertForbidden();
    }
}
