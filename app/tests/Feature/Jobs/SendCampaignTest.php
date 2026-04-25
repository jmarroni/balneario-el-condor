<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\SendCampaign;
use App\Mail\NewsletterCampaignMail;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendCampaignTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_campaign_sends_to_confirmed_only_not_pending_or_unsubscribed(): void
    {
        Mail::fake();

        NewsletterSubscriber::factory()->count(3)->create(['status' => 'confirmed']);
        NewsletterSubscriber::factory()->count(2)->create(['status' => 'pending']);
        NewsletterSubscriber::factory()->count(1)->create(['status' => 'unsubscribed']);

        $campaign = NewsletterCampaign::factory()->create(['status' => 'draft']);

        (new SendCampaign($campaign))->handle();

        Mail::assertSent(NewsletterCampaignMail::class, 3);

        $fresh = $campaign->fresh();
        $this->assertSame('sent', $fresh->status);
        $this->assertSame(3, $fresh->sent_count);
        $this->assertNotNull($fresh->sent_at);
    }

    public function test_send_campaign_processes_in_chunks_of_100(): void
    {
        Mail::fake();

        // 101 subscribers confirmed → fuerza al menos 2 iteraciones de chunkById(100).
        NewsletterSubscriber::factory()->count(101)->create(['status' => 'confirmed']);

        $campaign = NewsletterCampaign::factory()->create(['status' => 'draft']);

        (new SendCampaign($campaign))->handle();

        Mail::assertSent(NewsletterCampaignMail::class, 101);
        $this->assertSame(101, $campaign->fresh()->sent_count);
    }

    public function test_send_campaign_updates_sent_count_progress(): void
    {
        Mail::fake();

        NewsletterSubscriber::factory()->count(5)->create(['status' => 'confirmed']);

        $campaign = NewsletterCampaign::factory()->create([
            'status'     => 'draft',
            'sent_count' => 0,
        ]);

        (new SendCampaign($campaign))->handle();

        // Después del handle el sent_count refleja exactamente lo enviado.
        $this->assertSame(5, $campaign->fresh()->sent_count);
    }

    public function test_send_campaign_marks_sent_when_done(): void
    {
        Mail::fake();

        NewsletterSubscriber::factory()->count(2)->create(['status' => 'confirmed']);

        $campaign = NewsletterCampaign::factory()->create([
            'status'  => 'draft',
            'sent_at' => null,
        ]);

        (new SendCampaign($campaign))->handle();

        $fresh = $campaign->fresh();
        $this->assertSame('sent', $fresh->status);
        $this->assertNotNull($fresh->sent_at);
    }

    public function test_send_campaign_failure_marks_failed(): void
    {
        $campaign = NewsletterCampaign::factory()->create(['status' => 'draft']);

        (new SendCampaign($campaign))->failed(new Exception('SMTP unreachable'));

        $this->assertSame('failed', $campaign->fresh()->status);
    }

    public function test_send_campaign_replaces_unsubscribe_url_in_body(): void
    {
        Mail::fake();

        $subscriber = NewsletterSubscriber::factory()->create([
            'status'             => 'confirmed',
            'confirmation_token' => 'token-replace-test-xyz',
        ]);

        $campaign = NewsletterCampaign::factory()->create([
            'status'    => 'draft',
            'body_html' => '<p>Hola, hacé click <a href="{{unsubscribe_url}}">acá</a>.</p>',
        ]);

        (new SendCampaign($campaign))->handle();

        Mail::assertSent(NewsletterCampaignMail::class, function ($mail) use ($subscriber) {
            $rendered = $mail->render();

            return str_contains($rendered, $subscriber->confirmation_token)
                && ! str_contains($rendered, '{{unsubscribe_url}}');
        });
    }
}
