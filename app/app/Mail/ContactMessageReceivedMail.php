<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Notificación al admin cuando entra un mensaje desde el formulario de
 * contacto público (web o API). El destinatario se setea en el controller
 * (config('mail.admin_address')).
 */
class ContactMessageReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $message)
    {
    }

    public function envelope(): Envelope
    {
        $subject = $this->message->subject !== null && $this->message->subject !== ''
            ? $this->message->subject
            : '(sin asunto)';

        return new Envelope(
            subject: 'Nuevo mensaje de contacto: ' . $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.contact-message-received',
            with: [
                'message' => $this->message,
            ],
        );
    }
}
