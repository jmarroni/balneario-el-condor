<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\NearbyPlace;

class AdminNearbyPlacesTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        NearbyPlace::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/nearby-places')->assertOk()->assertSee('Lugares cercanos');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/nearby-places')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/nearby-places', [
            'title'       => 'Playa de prueba',
            'description' => 'Una playa cercana',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('nearby_places', ['title' => 'Playa de prueba']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/nearby-places', [])
            ->assertSessionHasErrors(['title']);
    }

    public function test_admin_can_update(): void
    {
        $place = NearbyPlace::factory()->create();
        $this->asAdmin()->put("/admin/nearby-places/{$place->id}", [
            'title' => 'Nuevo título',
        ])->assertRedirect();
        $this->assertDatabaseHas('nearby_places', ['id' => $place->id, 'title' => 'Nuevo título']);
    }

    public function test_admin_can_delete(): void
    {
        $place = NearbyPlace::factory()->create();
        $this->asAdmin()->delete("/admin/nearby-places/{$place->id}")->assertRedirect();
        $this->assertDatabaseMissing('nearby_places', ['id' => $place->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $place = NearbyPlace::factory()->create();
        $this->asModerator()->delete("/admin/nearby-places/{$place->id}")->assertForbidden();
    }
}
