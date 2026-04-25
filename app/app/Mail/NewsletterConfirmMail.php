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
 * Mail de confirmación de suscripción al newsletter (double opt-in).
 * Se envía cuando un usuario se suscribe; contiene el link para confirmar.
 */
class NewsletterConfirmMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public NewsletterSubscriber $subscriber)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmá tu suscripción al newsletter',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.newsletter-confirm',
            with: [
                'subscriber' => $this->subscriber,
                'confirmUrl' => route(
                    'newsletter.confirm',
                    $this->subscriber->confirmation_token
                ),
            ],
        );
    }
}
