<?php

namespace Tests\Feature\Admin;

use App\Models\News;

class PoliciesTest extends AdminTestCase
{
    public function test_admin_can_manage_news(): void
    {
        $news = News::factory()->create();
        $this->assertTrue($this->admin->can('viewAny', News::class));
        $this->assertTrue($this->admin->can('update', $news));
        $this->assertTrue($this->admin->can('delete', $news));
    }

    public function test_editor_can_manage_news(): void
    {
        $news = News::factory()->create();
        $this->assertTrue($this->editor->can('viewAny', News::class));
        $this->assertTrue($this->editor->can('update', $news));
    }

    public function test_moderator_cannot_manage_news(): void
    {
        $this->assertFalse($this->moderator->can('viewAny', News::class));
        $this->assertFalse($this->moderator->can('create', News::class));
    }

    public function test_moderator_can_view_and_delete_moderable(): void
    {
        $classified = \App\Models\Classified::factory()->create();
        $this->assertTrue($this->moderator->can('viewAny', \App\Models\Classified::class));
        $this->assertTrue($this->moderator->can('delete', $classified));
        $this->assertFalse($this->moderator->can('update', $classified));
    }
}
