<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\ServiceProvider;

class AdminServiceProvidersTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        ServiceProvider::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/service-providers')->assertOk()->assertSee('Prestadores');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/service-providers')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/service-providers', [
            'name'          => 'Plomería Prueba',
            'contact_email' => 'test@example.com',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('service_providers', ['name' => 'Plomería Prueba']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/service-providers', [])
            ->assertSessionHasErrors(['name']);
    }

    public function test_admin_can_update(): void
    {
        $provider = ServiceProvider::factory()->create();
        $this->asAdmin()->put("/admin/service-providers/{$provider->id}", [
            'name' => 'Actualizado',
        ])->assertRedirect();
        $this->assertDatabaseHas('service_providers', ['id' => $provider->id, 'name' => 'Actualizado']);
    }

    public function test_admin_can_delete(): void
    {
        $provider = ServiceProvider::factory()->create();
        $this->asAdmin()->delete("/admin/service-providers/{$provider->id}")->assertRedirect();
        $this->assertSoftDeleted('service_providers', ['id' => $provider->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $provider = ServiceProvider::factory()->create();
        $this->asModerator()->delete("/admin/service-providers/{$provider->id}")->assertForbidden();
    }
}
