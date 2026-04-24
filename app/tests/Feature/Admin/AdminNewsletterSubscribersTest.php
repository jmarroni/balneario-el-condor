<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\NewsletterSubscriber;

class AdminNewsletterSubscribersTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        NewsletterSubscriber::factory()->count(3)->create();
        $this->asAdmin()->get('/admin/newsletter-subscribers')
            ->assertOk()
            ->assertSee('Suscriptores');
    }

    public function test_moderator_can_view(): void
    {
        NewsletterSubscriber::factory()->count(2)->create();
        $this->asModerator()->get('/admin/newsletter-subscribers')->assertOk();
    }

    public function test_moderator_can_delete(): void
    {
        $sub = NewsletterSubscriber::factory()->create();
        $this->asModerator()->delete("/admin/newsletter-subscribers/{$sub->id}")->assertRedirect();
        $this->assertDatabaseMissing('newsletter_subscribers', ['id' => $sub->id]);
    }

    public function test_editor_can_delete(): void
    {
        $sub = NewsletterSubscriber::factory()->create();
        $this->asEditor()->delete("/admin/newsletter-subscribers/{$sub->id}")->assertRedirect();
        $this->assertDatabaseMissing('newsletter_subscribers', ['id' => $sub->id]);
    }

    public function test_admin_downloads_csv(): void
    {
        NewsletterSubscriber::factory()->create([
            'email'  => 'csv-user@example.com',
            'status' => 'confirmed',
        ]);

        $response = $this->asAdmin()->get('/admin/newsletter-subscribers/export');
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('email,status,subscribed_at,confirmed_at,unsubscribed_at,ip_address', $content);
        $this->assertStringContainsString('csv-user@example.com', $content);
    }
}
