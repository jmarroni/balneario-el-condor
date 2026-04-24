<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Rental;

class AdminRentalsTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Rental::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/rentals')->assertOk()->assertSee('Alquileres');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/rentals')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/rentals', [
            'title'  => 'Alquiler de prueba',
            'places' => 4,
            'phone'  => '+54 9 2920 123456',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('rentals', ['title' => 'Alquiler de prueba', 'places' => 4]);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/rentals', [])
            ->assertSessionHasErrors(['title']);
    }

    public function test_admin_can_update(): void
    {
        $rental = Rental::factory()->create();
        $this->asAdmin()->put("/admin/rentals/{$rental->id}", [
            'title' => 'Nuevo título',
        ])->assertRedirect();
        $this->assertDatabaseHas('rentals', ['id' => $rental->id, 'title' => 'Nuevo título']);
    }

    public function test_admin_can_delete(): void
    {
        $rental = Rental::factory()->create();
        $this->asAdmin()->delete("/admin/rentals/{$rental->id}")->assertRedirect();
        $this->assertSoftDeleted('rentals', ['id' => $rental->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $rental = Rental::factory()->create();
        $this->asModerator()->delete("/admin/rentals/{$rental->id}")->assertForbidden();
    }
}
