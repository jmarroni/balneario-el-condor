<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Envía el body HTML de una campaña a un subscriber concreto.
 *
 * NO implementa ShouldQueue: el `SendCampaign` job ya corre en queue y
 * envía cada mail síncrono dentro del worker para mantener `sent_count`
 * consistente y evitar el fan-out de N jobs por campaña.
 */
class NewsletterCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NewsletterCampaign $campaign,
        public NewsletterSubscriber $subscriber,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
            tags: ['newsletter-campaign', "campaign-{$this->campaign->id}"],
        );
    }

    public function content(): Content
    {
        $unsubscribeUrl = route('newsletter.unsubscribe', $this->subscriber->confirmation_token);

        return new Content(
            markdown: 'mail.campaign',
            with: [
                'campaign'       => $this->campaign,
                'subscriber'     => $this->subscriber,
                'unsubscribeUrl' => $unsubscribeUrl,
                'body'           => $this->replaceVars($this->campaign->body_html ?? '', $unsubscribeUrl),
            ],
        );
    }

    /**
     * Reemplaza variables placeholders del body HTML antes de renderizar el template.
     * No tenemos `name` en `newsletter_subscribers`, usamos `email` como fallback.
     */
    private function replaceVars(string $html, string $unsubscribeUrl): string
    {
        return str_replace(
            ['{{name}}', '{{email}}', '{{unsubscribe_url}}'],
            [
                e($this->subscriber->email),
                e($this->subscriber->email),
                $unsubscribeUrl,
            ],
            $html
        );
    }
}
