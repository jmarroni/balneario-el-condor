<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirmación al usuario que completó la inscripción a un evento. Incluye
 * un resumen de los datos cargados (con campos extra del legacy si aplican).
 */
class EventRegistrationConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Event $event,
        public EventRegistration $registration,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Inscripción confirmada: ' . $this->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.event-registration-confirmation',
            with: [
                'event'        => $this->event,
                'registration' => $this->registration,
                'eventUrl'     => route('eventos.show', $this->event),
            ],
        );
    }
}
