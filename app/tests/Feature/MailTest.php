<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\AdvertisingContactReceivedMail;
use App\Mail\ClassifiedContactMail;
use App\Mail\ContactMessageReceivedMail;
use App\Mail\EventRegistrationConfirmationMail;
use App\Mail\NewsletterConfirmMail;
use App\Mail\NewsletterUnsubscribedMail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\Classified;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\NewsletterSubscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------
    // Public web flow
    // -------------------------------------------------------------------

    public function test_contact_form_queues_admin_notification(): void
    {
        Mail::fake();

        $response = $this->post(route('contacto.store'), [
            'name'    => 'Juan Tester',
            'email'   => 'juan@example.test',
            'phone'   => '+54 9 11 1234-5678',
            'subject' => 'Consulta sobre alojamiento',
            'message' => 'Querría información sobre disponibilidad en enero próximo.',
        ]);

        $response->assertRedirect(route('contacto.show'));

        Mail::assertQueued(ContactMessageReceivedMail::class, function ($mail) {
            return $mail->hasTo(config('mail.admin_address'));
        });
    }

    public function test_api_public_contact_queues_admin_notification(): void
    {
        Mail::fake();

        $response = $this->postJson(route('api.v1.contact'), [
            'name'    => 'Ana API',
            'email'   => 'ana@example.test',
            'subject' => 'Consulta vía API',
            'message' => 'Mensaje suficientemente largo para pasar la validación de longitud.',
        ]);

        $response->assertCreated();

        Mail::assertQueued(ContactMessageReceivedMail::class, function ($mail) {
            return $mail->hasTo(config('mail.admin_address'));
        });
    }

    public function test_newsletter_subscribe_queues_confirmation(): void
    {
        Mail::fake();

        $this->post(route('newsletter.subscribe'), [
            'email' => 'nuevo@test.example',
        ])->assertRedirect(route('newsletter.form'));

        Mail::assertQueued(NewsletterConfirmMail::class, function ($mail) {
            return $mail->hasTo('nuevo@test.example');
        });
    }

    public function test_newsletter_confirm_queues_welcome(): void
    {
        Mail::fake();

        $sub = NewsletterSubscriber::factory()->create([
            'email'              => 'pending@test.example',
            'status'             => 'pending',
            'confirmation_token' => 'token-confirm-test',
            'confirmed_at'       => null,
        ]);

        $this->get(route('newsletter.confirm', $sub->confirmation_token))
            ->assertOk();

        Mail::assertQueued(NewsletterWelcomeMail::class, function ($mail) use ($sub) {
            return $mail->hasTo($sub->email);
        });
    }

    public function test_newsletter_unsubscribe_queues_goodbye(): void
    {
        Mail::fake();

        $sub = NewsletterSubscriber::factory()->create([
            'email'              => 'leaving@test.example',
            'status'             => 'confirmed',
            'confirmation_token' => 'token-unsub-test',
        ]);

        $this->get(route('newsletter.unsubscribe', $sub->confirmation_token))
            ->assertOk();

        Mail::assertQueued(NewsletterUnsubscribedMail::class, function ($mail) use ($sub) {
            return $mail->hasTo($sub->email);
        });
    }

    public function test_classified_contact_queues_to_owner(): void
    {
        Mail::fake();

        $classified = Classified::factory()->create([
            'slug'          => 'aviso-mail-test',
            'title'         => 'Vendo bicicleta playera',
            'contact_email' => 'duenio@test.example',
        ]);

        $this->post(route('clasificados.contact', $classified), [
            'name'    => 'Comprador Curioso',
            'email'   => 'comprador@test.example',
            'message' => 'Hola, ¿la bici está todavía disponible? Me interesa mucho, gracias.',
        ])->assertRedirect();

        Mail::assertQueued(ClassifiedContactMail::class, function ($mail) use ($classified) {
            return $mail->hasTo($classified->contact_email);
        });
    }

    public function test_publicite_queues_admin_notification(): void
    {
        Mail::fake();

        $this->post(route('publicite.store'), [
            'name'    => 'Comercio Local',
            'email'   => 'comercio@test.example',
            'message' => 'Quisiera publicar un banner en la sección de eventos durante el verano próximo.',
        ])->assertRedirect(route('publicite.show'));

        Mail::assertQueued(AdvertisingContactReceivedMail::class, function ($mail) {
            return $mail->hasTo(config('mail.admin_address'));
        });
    }

    public function test_event_registration_queues_confirmation(): void
    {
        Mail::fake();

        $event = Event::factory()->create([
            'title'                 => 'Fiesta de la Primavera 2026',
            'slug'                  => 'fiesta-primavera-mail-test',
            'accepts_registrations' => true,
        ]);

        $this->post(route('eventos.register', $event), [
            'name'  => 'Sofía Inscripta',
            'email' => 'sofia@test.example',
            'phone' => '+54 9 2920 555-000',
        ])->assertRedirect(route('eventos.show', $event));

        Mail::assertQueued(EventRegistrationConfirmationMail::class, function ($mail) {
            return $mail->hasTo('sofia@test.example');
        });
    }

    // -------------------------------------------------------------------
    // Mailable contract checks
    // -------------------------------------------------------------------

    public function test_contact_mail_has_correct_subject(): void
    {
        $message = ContactMessage::factory()->create([
            'name'    => 'Juan Pruebas',
            'subject' => 'Asunto Específico',
            'message' => 'Mensaje de prueba para verificar el envelope del mailable.',
        ]);

        $mail = new ContactMessageReceivedMail($message);
        $envelope = $mail->envelope();

        $this->assertStringContainsString('Asunto Específico', $envelope->subject);
        $this->assertStringContainsString('Nuevo mensaje de contacto', $envelope->subject);
    }

    public function test_all_mailables_implement_should_queue(): void
    {
        $mailables = [
            ContactMessageReceivedMail::class,
            NewsletterConfirmMail::class,
            NewsletterWelcomeMail::class,
            NewsletterUnsubscribedMail::class,
            ClassifiedContactMail::class,
            AdvertisingContactReceivedMail::class,
            EventRegistrationConfirmationMail::class,
        ];

        foreach ($mailables as $class) {
            $this->assertContains(
                ShouldQueue::class,
                class_implements($class),
                "{$class} debe implementar ShouldQueue para encolarse vía Redis"
            );
        }
    }

    public function test_event_registration_mail_includes_event_title_in_subject(): void
    {
        $event = Event::factory()->create([
            'title' => 'Encuentro Costero 2026',
            'slug'  => 'encuentro-costero-mail-subject',
        ]);
        $registration = EventRegistration::factory()->create([
            'event_id' => $event->id,
            'email'    => 'inscripto@test.example',
        ]);

        $mail = new EventRegistrationConfirmationMail($event, $registration);

        $this->assertStringContainsString('Encuentro Costero 2026', $mail->envelope()->subject);
    }
}
