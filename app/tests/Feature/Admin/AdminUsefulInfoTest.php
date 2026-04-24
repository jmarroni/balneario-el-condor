<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\UsefulInfo;

class AdminUsefulInfoTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        UsefulInfo::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/useful-info')->assertOk()->assertSee('Información útil');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/useful-info')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $response = $this->asEditor()->post('/admin/useful-info', [
            'title'      => 'Policía',
            'phone'      => '911',
            'sort_order' => 1,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('useful_info', ['title' => 'Policía', 'phone' => '911']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/useful-info', [])
            ->assertSessionHasErrors(['title']);
    }

    public function test_admin_can_update(): void
    {
        $item = UsefulInfo::factory()->create();
        $this->asAdmin()->put("/admin/useful-info/{$item->id}", [
            'title' => 'Bomberos',
        ])->assertRedirect();
        $this->assertDatabaseHas('useful_info', ['id' => $item->id, 'title' => 'Bomberos']);
    }

    public function test_admin_can_delete(): void
    {
        $item = UsefulInfo::factory()->create();
        $this->asAdmin()->delete("/admin/useful-info/{$item->id}")->assertRedirect();
        $this->assertDatabaseMissing('useful_info', ['id' => $item->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $item = UsefulInfo::factory()->create();
        $this->asModerator()->delete("/admin/useful-info/{$item->id}")->assertForbidden();
    }
}
