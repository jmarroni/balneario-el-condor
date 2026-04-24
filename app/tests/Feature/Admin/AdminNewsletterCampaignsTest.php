<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Jobs\SendCampaign;
use App\Models\NewsletterCampaign;
use Illuminate\Support\Facades\Bus;

class AdminNewsletterCampaignsTest extends AdminTestCase
{
    public function test_admin_sees_index(): void
    {
        NewsletterCampaign::factory()->count(2)->create();
        $this->asAdmin()->get('/admin/newsletter-campaigns')
            ->assertOk()
            ->assertSee('Campañas');
    }

    public function test_moderator_is_forbidden(): void
    {
        $this->asModerator()->get('/admin/newsletter-campaigns')->assertForbidden();
    }

    public function test_admin_can_create(): void
    {
        $response = $this->asAdmin()->post('/admin/newsletter-campaigns', [
            'subject'   => 'Novedades de abril',
            'body_html' => '<p>Hola</p>',
            'status'    => 'draft',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('newsletter_campaigns', ['subject' => 'Novedades de abril']);
    }

    public function test_store_validates_required(): void
    {
        $this->asAdmin()->post('/admin/newsletter-campaigns', [])
            ->assertSessionHasErrors(['subject', 'body_html']);
    }

    public function test_admin_can_update(): void
    {
        $campaign = NewsletterCampaign::factory()->create();
        $this->asAdmin()->put("/admin/newsletter-campaigns/{$campaign->id}", [
            'subject'   => 'Nuevo asunto',
            'body_html' => '<p>Actualizado</p>',
        ])->assertRedirect();
        $this->assertDatabaseHas('newsletter_campaigns', ['id' => $campaign->id, 'subject' => 'Nuevo asunto']);
    }

    public function test_admin_can_delete(): void
    {
        $campaign = NewsletterCampaign::factory()->create();
        $this->asAdmin()->delete("/admin/newsletter-campaigns/{$campaign->id}")->assertRedirect();
        $this->assertSoftDeleted('newsletter_campaigns', ['id' => $campaign->id]);
    }

    public function test_admin_dispatches_send(): void
    {
        Bus::fake();

        $campaign = NewsletterCampaign::factory()->create();

        $this->asAdmin()->post("/admin/newsletter-campaigns/{$campaign->id}/send")->assertRedirect();

        Bus::assertDispatched(SendCampaign::class, fn ($job) => $job->campaign->id === $campaign->id);
    }
}
