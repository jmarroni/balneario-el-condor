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
 * Confirmación de baja del newsletter. Incluye link para volver a suscribirse.
 */
class NewsletterUnsubscribedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu baja del newsletter fue procesada',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.newsletter-unsubscribed',
            with: [
                'subscriber'   => $this->subscriber,
                'resubscribeUrl' => route('newsletter.form'),
            ],
        );
    }
}
