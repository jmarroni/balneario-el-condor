<?php

declare(strict_types=1);

namespace Tests\Feature\Public;

use App\Models\AdvertisingContact;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------
    // Pages genéricas
    // -----------------------------------------------------------------

    public function test_page_show_renders_published(): void
    {
        $page = Page::factory()->create([
            'slug'      => 'historia-del-balneario-test',
            'title'     => 'Historia del Balneario Test',
            'content'   => 'Aquí narramos la fundación del pueblo costero y sus primeros años de balneario popular.',
            'published' => true,
        ]);

        $this->get(route('pages.show', $page))
            ->assertOk()
            ->assertSee('Historia del Balneario Test')
            ->assertSee('fundación del pueblo costero');
    }

    public function test_page_show_404_when_not_published(): void
    {
        $page = Page::factory()->create([
            'slug'      => 'borrador-test-no-publicado',
            'title'     => 'Borrador en revisión',
            'published' => false,
        ]);

        $this->get(route('pages.show', $page))->assertNotFound();
    }

    // -----------------------------------------------------------------
    // Contacto
    // -----------------------------------------------------------------

    public function test_contacto_form_renders(): void
    {
        $this->get(route('contacto.show'))
            ->assertOk()
            ->assertSee('Contactanos')
            ->assertSee('name="message"', false)
            ->assertSee('name="email"', false);
    }

    public function test_contacto_validates_required(): void
    {
        $response = $this->from(route('contacto.show'))
            ->post(route('contacto.store'), [
                'name'    => '',
                'email'   => 'not-an-email',
                'message' => 'corto',
            ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_contacto_store_creates_message(): void
    {
        $response = $this->post(route('contacto.store'), [
            'name'    => 'María Visitante',
            'email'   => 'maria@example.test',
            'phone'   => '+54 9 2920 333111',
            'subject' => 'Consulta sobre temporada',
            'message' => 'Hola, quería consultar sobre el alojamiento en enero.',
        ]);

        $response->assertRedirect(route('contacto.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name'    => 'María Visitante',
            'email'   => 'maria@example.test',
            'subject' => 'Consulta sobre temporada',
            'read'    => false,
        ]);

        $msg = ContactMessage::where('email', 'maria@example.test')->firstOrFail();
        $this->assertNotNull($msg->ip_address);
    }

    public function test_contacto_honeypot_rejects_bot(): void
    {
        $response = $this->from(route('contacto.show'))
            ->post(route('contacto.store'), [
                'name'             => 'Bot Spammer',
                'email'            => 'bot@example.test',
                'message'          => 'mensaje promocional largo y aburrido aquí',
                'captcha_honeypot' => 'http://spam.example/buy-now',
            ]);

        $response->assertSessionHasErrors(['captcha_honeypot']);
        $this->assertDatabaseCount('contact_messages', 0);
    }

    // -----------------------------------------------------------------
    // Newsletter
    // -----------------------------------------------------------------

    public function test_newsletter_subscribe_creates_pending(): void
    {
        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'lectora@example.test',
        ]);

        $response->assertRedirect(route('newsletter.form'));
        $response->assertSessionHas('success');

        $sub = NewsletterSubscriber::where('email', 'lectora@example.test')->firstOrFail();
        $this->assertSame('pending', $sub->status);
        $this->assertNotNull($sub->confirmation_token);
        $this->assertNotNull($sub->subscribed_at);
        $this->assertNull($sub->confirmed_at);
    }

    public function test_newsletter_subscribe_duplicate_email_updates(): void
    {
        $existing = NewsletterSubscriber::create([
            'email'              => 'repeat@example.test',
            'status'             => 'pending',
            'confirmation_token' => 'old-token-' . Str::random(20),
            'subscribed_at'      => now()->subDays(5),
        ]);

        $oldToken = $existing->confirmation_token;

        $response = $this->post(route('newsletter.subscribe'), [
            'email' => 'repeat@example.test',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseCount('newsletter_subscribers', 1);

        $existing->refresh();
        $this->assertNotSame($oldToken, $existing->confirmation_token);
        $this->assertSame('pending', $existing->status);
    }

    public function test_newsletter_confirm_activates(): void
    {
        $token = 'confirm-token-' . Str::random(30);

        $sub = NewsletterSubscriber::create([
            'email'              => 'tobeconfirmed@example.test',
            'status'             => 'pending',
            'confirmation_token' => $token,
            'subscribed_at'      => now(),
        ]);

        $this->get(route('newsletter.confirm', $token))
            ->assertOk()
            ->assertSee('Suscripción confirmada')
            ->assertSee('tobeconfirmed@example.test');

        $sub->refresh();
        $this->assertSame('confirmed', $sub->status);
        $this->assertNotNull($sub->confirmed_at);
    }

    public function test_newsletter_unsubscribe_works(): void
    {
        $token = 'unsub-token-' . Str::random(30);

        $sub = NewsletterSubscriber::create([
            'email'              => 'gone@example.test',
            'status'             => 'confirmed',
            'confirmation_token' => $token,
            'subscribed_at'      => now()->subDays(30),
            'confirmed_at'       => now()->subDays(30),
        ]);

        $this->get(route('newsletter.unsubscribe', $token))
            ->assertOk()
            ->assertSee('Te diste de baja')
            ->assertSee('gone@example.test');

        $sub->refresh();
        $this->assertSame('unsubscribed', $sub->status);
        $this->assertNotNull($sub->unsubscribed_at);
    }

    // -----------------------------------------------------------------
    // Publicite
    // -----------------------------------------------------------------

    public function test_publicite_form_renders(): void
    {
        $this->get(route('publicite.show'))
            ->assertOk()
            ->assertSee('Publicitate')
            ->assertSee('name="zone"', false)
            ->assertSee('name="message"', false);
    }

    public function test_publicite_store_creates_record(): void
    {
        $response = $this->post(route('publicite.store'), [
            'name'      => 'Comerciante',
            'last_name' => 'del Pueblo',
            'email'     => 'comercio@example.test',
            'message'   => 'Quisiera saber tarifas para publicitar mi cabaña en el sidebar de eventos.',
            'zone'      => 'sidebar',
        ]);

        $response->assertRedirect(route('publicite.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('advertising_contacts', [
            'name'      => 'Comerciante',
            'last_name' => 'del Pueblo',
            'email'     => 'comercio@example.test',
            'zone'      => 'sidebar',
            'read'      => false,
        ]);

        $ad = AdvertisingContact::where('email', 'comercio@example.test')->firstOrFail();
        $this->assertNull($ad->legacy_id);
    }
}
