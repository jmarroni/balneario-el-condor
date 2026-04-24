<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Classified;
use App\Models\ClassifiedCategory;

class AdminClassifiedsTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Classified::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/classifieds')->assertOk()->assertSee('Clasificados');
    }

    public function test_moderator_can_see_index(): void
    {
        Classified::factory()->count(2)->create();
        $this->asModerator()->get('/admin/classifieds')->assertOk();
    }

    public function test_editor_can_create(): void
    {
        $cat = ClassifiedCategory::factory()->create();

        $response = $this->asEditor()->post('/admin/classifieds', [
            'title' => 'Vendo bicicleta',
            'description' => 'Bicicleta en buen estado.',
            'classified_category_id' => $cat->id,
            'contact_name' => 'Juan',
            'contact_email' => 'juan@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('classifieds', ['title' => 'Vendo bicicleta']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/classifieds', [])
            ->assertSessionHasErrors(['title', 'description']);
    }

    public function test_admin_can_update(): void
    {
        $classified = Classified::factory()->create();

        $this->asAdmin()->put("/admin/classifieds/{$classified->id}", [
            'title' => 'Nuevo título',
            'description' => $classified->description,
        ])->assertRedirect();

        $this->assertDatabaseHas('classifieds', [
            'id' => $classified->id,
            'title' => 'Nuevo título',
        ]);
    }

    public function test_admin_can_delete(): void
    {
        $classified = Classified::factory()->create();

        $this->asAdmin()->delete("/admin/classifieds/{$classified->id}")->assertRedirect();
        $this->assertSoftDeleted('classifieds', ['id' => $classified->id]);
    }

    public function test_moderator_can_delete(): void
    {
        $classified = Classified::factory()->create();

        $this->asModerator()->delete("/admin/classifieds/{$classified->id}")->assertRedirect();
        $this->assertSoftDeleted('classifieds', ['id' => $classified->id]);
    }

    public function test_moderator_cannot_update(): void
    {
        $classified = Classified::factory()->create();

        $this->asModerator()->put("/admin/classifieds/{$classified->id}", [
            'title' => 'Intento mod',
            'description' => 'no permitido',
        ])->assertForbidden();
    }

    public function test_moderator_cannot_create(): void
    {
        $this->asModerator()->get('/admin/classifieds/create')->assertForbidden();

        $this->asModerator()->post('/admin/classifieds', [
            'title' => 'Intento',
            'description' => 'No debería poder crear',
        ])->assertForbidden();
    }
}
