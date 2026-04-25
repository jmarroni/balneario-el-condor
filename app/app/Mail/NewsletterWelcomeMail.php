<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Bienvenida al newsletter, post-confirmación. Incluye link de baja.
 */
class NewsletterWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Suscripción confirmada!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.newsletter-welcome',
            with: [
                'subscriber'    => $this->subscriber,
                'unsubscribeUrl' => route(
                    'newsletter.unsubscribe',
                    $this->subscriber->confirmation_token
                ),
            ],
        );
    }
}
