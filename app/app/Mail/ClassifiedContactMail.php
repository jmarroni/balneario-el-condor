<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Classified;
use App\Models\ClassifiedContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Reenvía al owner del clasificado el mensaje que un visitante dejó
 * en el form de contacto del aviso. El destino es contact_email del clasificado.
 */
class ClassifiedContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Classified $classified,
        public ClassifiedContact $contact,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Consulta sobre tu clasificado: ' . $this->classified->title,
            replyTo: [$this->contact->contact_email],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.classified-contact',
            with: [
                'classified' => $this->classified,
                'contact'    => $this->contact,
                'classifiedUrl' => route('clasificados.show', $this->classified),
            ],
        );
    }
}
