<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\News;
use App\Models\NewsCategory;

class AdminNewsTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        News::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/news')->assertOk()->assertSee('Noticias');
    }

    public function test_moderator_cannot_see_index(): void
    {
        $this->asModerator()->get('/admin/news')->assertForbidden();
    }

    public function test_editor_can_create(): void
    {
        $cat = NewsCategory::factory()->create();
        $response = $this->asEditor()->post('/admin/news', [
            'title'            => 'Nueva noticia',
            'body'             => 'Cuerpo largo.',
            'news_category_id' => $cat->id,
            'published_at'     => now()->format('Y-m-d\TH:i'),
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('news', ['title' => 'Nueva noticia']);
    }

    public function test_store_validates_required(): void
    {
        $this->asEditor()->post('/admin/news', [])
            ->assertSessionHasErrors(['title', 'body']);
    }

    public function test_admin_can_update(): void
    {
        $news = News::factory()->create();
        $this->asAdmin()->put("/admin/news/{$news->id}", [
            'title' => 'Nuevo título',
            'body'  => $news->body,
        ])->assertRedirect();
        $this->assertDatabaseHas('news', ['id' => $news->id, 'title' => 'Nuevo título']);
    }

    public function test_admin_can_delete(): void
    {
        $news = News::factory()->create();
        $this->asAdmin()->delete("/admin/news/{$news->id}")->assertRedirect();
        $this->assertSoftDeleted('news', ['id' => $news->id]);
    }

    public function test_moderator_cannot_delete(): void
    {
        $news = News::factory()->create();
        $this->asModerator()->delete("/admin/news/{$news->id}")->assertForbidden();
    }
}
