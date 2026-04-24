<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\ContactMessage;

class AdminContactMessagesTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        ContactMessage::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/contact-messages')->assertOk()->assertSee('Mensajes');
    }

    public function test_moderator_can_view_and_delete(): void
    {
        $msg = ContactMessage::factory()->create();

        $this->asModerator()->get('/admin/contact-messages')->assertOk();
        $this->asModerator()->get("/admin/contact-messages/{$msg->id}")->assertOk();

        $this->asModerator()->delete("/admin/contact-messages/{$msg->id}")->assertRedirect();
        $this->assertDatabaseMissing('contact_messages', ['id' => $msg->id]);
    }

    public function test_admin_marks_read(): void
    {
        $msg = ContactMessage::factory()->create(['read' => false]);

        $this->asAdmin()->patch("/admin/contact-messages/{$msg->id}/mark-read")->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['id' => $msg->id, 'read' => true]);

        // Toggle de vuelta
        $this->asAdmin()->patch("/admin/contact-messages/{$msg->id}/mark-read")->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['id' => $msg->id, 'read' => false]);
    }

    public function test_editor_marks_read(): void
    {
        $msg = ContactMessage::factory()->create(['read' => false]);

        $this->asEditor()->patch("/admin/contact-messages/{$msg->id}/mark-read")->assertRedirect();
        $this->assertDatabaseHas('contact_messages', ['id' => $msg->id, 'read' => true]);
    }

    public function test_moderator_cannot_mark_read(): void
    {
        $msg = ContactMessage::factory()->create(['read' => false]);

        // moderator tiene view + delete, NO update
        $this->asModerator()->patch("/admin/contact-messages/{$msg->id}/mark-read")->assertForbidden();
    }

    public function test_filter_by_read(): void
    {
        ContactMessage::factory()->create(['read' => false, 'name' => 'Not Read One']);
        ContactMessage::factory()->create(['read' => true, 'name' => 'Already Read']);

        $this->asAdmin()->get('/admin/contact-messages?read=0')
            ->assertOk()
            ->assertSee('Not Read One')
            ->assertDontSee('Already Read');
    }
}
