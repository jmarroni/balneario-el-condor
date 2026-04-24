<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Page;

class AdminPagesTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        Page::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/pages')->assertOk()->assertSee('Páginas');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/pages')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/pages', [
            'slug'      => 'mi-pagina',
            'title'     => 'Mi página',
            'content'   => 'Contenido de la página.',
            'published' => '1',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('pages', ['slug' => 'mi-pagina', 'title' => 'Mi página']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/pages', [])
            ->assertSessionHasErrors(['slug', 'title']);
    }

    public function test_admin_can_update(): void
    {
        $page = Page::factory()->create();
        $this->asAdmin()->put("/admin/pages/{$page->slug}", [
            'slug'  => $page->slug,
            'title' => 'Nuevo título',
        ])->assertRedirect();
        $this->assertDatabaseHas('pages', ['id' => $page->id, 'title' => 'Nuevo título']);
    }

    public function test_admin_can_delete(): void
    {
        $page = Page::factory()->create();
        $this->asAdmin()->delete("/admin/pages/{$page->slug}")->assertRedirect();
        $this->assertSoftDeleted('pages', ['id' => $page->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $page = Page::factory()->create();
        $this->asModerator()->delete("/admin/pages/{$page->slug}")->assertForbidden();
    }
}
