<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Lodging;

class AdminLodgingsTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Lodging::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/lodgings')->assertOk()->assertSee('Alojamientos');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/lodgings')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/lodgings', [
            'name' => 'Hotel Prueba',
            'type' => 'hotel',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('lodgings', ['name' => 'Hotel Prueba', 'type' => 'hotel']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/lodgings', [])
            ->assertSessionHasErrors(['name', 'type']);
    }

    public function test_admin_can_update(): void
    {
        $lodging = Lodging::factory()->create();
        $this->asAdmin()->put("/admin/lodgings/{$lodging->id}", [
            'name' => 'Nuevo nombre',
            'type' => $lodging->type,
        ])->assertRedirect();
        $this->assertDatabaseHas('lodgings', ['id' => $lodging->id, 'name' => 'Nuevo nombre']);
    }

    public function test_admin_can_delete(): void
    {
        $lodging = Lodging::factory()->create();
        $this->asAdmin()->delete("/admin/lodgings/{$lodging->id}")->assertRedirect();
        $this->assertSoftDeleted('lodgings', ['id' => $lodging->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $lodging = Lodging::factory()->create();
        $this->asModerator()->delete("/admin/lodgings/{$lodging->id}")->assertForbidden();
    }
}
