<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Recipe;

class AdminRecipesTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Recipe::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/recipes')->assertOk()->assertSee('Recetas');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/recipes')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/recipes', [
            'title'        => 'Mejillones a la criolla',
            'ingredients'  => "- Mejillones\n- Cebolla\n- Tomate",
            'instructions' => 'Cocinar todo junto durante 20 minutos.',
            'author'       => 'Chef local',
            'published_on' => now()->format('Y-m-d'),
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('recipes', ['title' => 'Mejillones a la criolla']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/recipes', [])
            ->assertSessionHasErrors(['title', 'ingredients', 'instructions']);
    }

    public function test_admin_can_update(): void
    {
        $recipe = Recipe::factory()->create();
        $this->asAdmin()->put("/admin/recipes/{$recipe->id}", [
            'title'        => 'Nuevo título',
            'ingredients'  => $recipe->ingredients,
            'instructions' => $recipe->instructions,
        ])->assertRedirect();
        $this->assertDatabaseHas('recipes', ['id' => $recipe->id, 'title' => 'Nuevo título']);
    }

    public function test_admin_can_delete(): void
    {
        $recipe = Recipe::factory()->create();
        $this->asAdmin()->delete("/admin/recipes/{$recipe->id}")->assertRedirect();
        $this->assertSoftDeleted('recipes', ['id' => $recipe->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $recipe = Recipe::factory()->create();
        $this->asModerator()->delete("/admin/recipes/{$recipe->id}")->assertForbidden();
    }
}
