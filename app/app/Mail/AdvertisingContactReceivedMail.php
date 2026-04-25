<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\AdvertisingContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notificación al admin cuando entra una consulta de "Publicite con nosotros".
 */
class AdvertisingContactReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public AdvertisingContact $advertisingContact)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nueva consulta de publicidad de ' . $this->advertisingContact->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.advertising-contact-received',
            with: [
                'ad' => $this->advertisingContact,
            ],
        );
    }
}
