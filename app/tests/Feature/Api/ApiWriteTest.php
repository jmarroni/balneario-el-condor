<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Classified;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Cache\RateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiWriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        // Asegurar que el rate limiter empieza limpio en cada test.
        $this->app->make(RateLimiter::class)->clear('api.v1.contact');
    }

    /**
     * @return array{0: User, 1: string}
     */
    private function userWithRole(string $role): array
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        $token = $user->createToken('test')->plainTextToken;

        return [$user, $token];
    }

    /**
     * @return array<string, string>
     */
    private function authHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    public function test_admin_creates_news_via_api(): void
    {
        [, $token] = $this->userWithRole('admin');
        $cat = NewsCategory::factory()->create();

        $response = $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/v1/news', [
                'title'            => 'Noticia desde la API',
                'body'             => 'Cuerpo extenso de la noticia para validar.',
                'news_category_id' => $cat->id,
                'published_at'     => now()->subHour()->toIso8601String(),
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Noticia desde la API')
            ->assertJsonPath('data.slug', 'noticia-desde-la-api');

        $this->assertDatabaseHas('news', [
            'title'            => 'Noticia desde la API',
            'slug'             => 'noticia-desde-la-api',
            'news_category_id' => $cat->id,
        ]);
    }

    public function test_editor_can_create_news(): void
    {
        [, $token] = $this->userWithRole('editor');
        $cat = NewsCategory::factory()->create();

        $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/v1/news', [
                'title'            => 'Editor crea noticia',
                'body'             => 'Cuerpo bastante largo para test.',
                'news_category_id' => $cat->id,
            ])
            ->assertCreated();
    }

    public function test_moderator_cannot_create_news(): void
    {
        [, $token] = $this->userWithRole('moderator');
        $cat = NewsCategory::factory()->create();

        $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/v1/news', [
                'title'            => 'Mod no puede',
                'body'             => 'No debería pasar.',
                'news_category_id' => $cat->id,
            ])
            ->assertStatus(403);
    }

    public function test_admin_updates_news(): void
    {
        [, $token] = $this->userWithRole('admin');
        $news = News::factory()->create(['title' => 'Original']);

        $this->withHeaders($this->authHeaders($token))
            ->putJson('/api/v1/news/'.$news->slug, [
                'title' => 'Título actualizado',
                'body'  => 'Body nuevo y suficientemente largo.',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Título actualizado');

        $this->assertDatabaseHas('news', [
            'id'    => $news->id,
            'title' => 'Título actualizado',
        ]);
    }

    public function test_admin_deletes_news_soft_delete(): void
    {
        [, $token] = $this->userWithRole('admin');
        $news = News::factory()->create();

        $this->withHeaders($this->authHeaders($token))
            ->deleteJson('/api/v1/news/'.$news->slug)
            ->assertNoContent();

        $this->assertSoftDeleted('news', ['id' => $news->id]);
    }

    public function test_moderator_can_delete_classified(): void
    {
        [, $token] = $this->userWithRole('moderator');
        $classified = Classified::factory()->create();

        $this->withHeaders($this->authHeaders($token))
            ->deleteJson('/api/v1/classifieds/'.$classified->slug)
            ->assertNoContent();

        $this->assertSoftDeleted('classifieds', ['id' => $classified->id]);
    }

    public function test_admin_creates_event(): void
    {
        [, $token] = $this->userWithRole('admin');

        $this->withHeaders($this->authHeaders($token))
            ->postJson('/api/v1/events', [
                'title'       => 'Festival de la Costa',
                'description' => 'Música en vivo, food trucks y juegos.',
                'starts_at'   => now()->addDays(10)->toIso8601String(),
                'ends_at'     => now()->addDays(11)->toIso8601String(),
                'location'    => 'Plaza central',
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Festival de la Costa');

        $this->assertDatabaseHas('events', ['title' => 'Festival de la Costa']);
    }

    public function test_moderator_cannot_mark_contact_message_read(): void
    {
        [, $token] = $this->userWithRole('moderator');
        $message = ContactMessage::factory()->create(['read' => false]);

        // moderator no tiene contact_messages.update.
        $this->withHeaders($this->authHeaders($token))
            ->patchJson('/api/v1/contact-messages/'.$message->id.'/mark-read')
            ->assertStatus(403);
    }

    public function test_admin_marks_contact_message_read(): void
    {
        [, $token] = $this->userWithRole('admin');
        $message = ContactMessage::factory()->create(['read' => false]);

        $this->withHeaders($this->authHeaders($token))
            ->patchJson('/api/v1/contact-messages/'.$message->id.'/mark-read')
            ->assertOk()
            ->assertJsonPath('data.read', true);

        $this->assertDatabaseHas('contact_messages', [
            'id'   => $message->id,
            'read' => true,
        ]);
    }

    public function test_moderator_can_delete_contact_message(): void
    {
        [, $token] = $this->userWithRole('moderator');
        $message = ContactMessage::factory()->create();

        $this->withHeaders($this->authHeaders($token))
            ->deleteJson('/api/v1/contact-messages/'.$message->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    public function test_moderator_can_delete_newsletter_subscriber(): void
    {
        [, $token] = $this->userWithRole('moderator');
        $sub = NewsletterSubscriber::factory()->create();

        $this->withHeaders($this->authHeaders($token))
            ->deleteJson('/api/v1/newsletter-subscribers/'.$sub->id)
            ->assertNoContent();

        $this->assertDatabaseMissing('newsletter_subscribers', ['id' => $sub->id]);
    }

    public function test_public_contact_submission_creates_record_without_auth(): void
    {
        $response = $this->postJson('/api/v1/contact', [
            'name'    => 'Juan Tester',
            'email'   => 'juan@example.com',
            'message' => 'Quiero consultar sobre alquileres de verano.',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'received_at'], 'meta' => ['message']]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'juan@example.com',
            'name'  => 'Juan Tester',
            'read'  => 0,
        ]);
    }

    public function test_public_contact_honeypot_rejects_bots(): void
    {
        $this->postJson('/api/v1/contact', [
            'name'             => 'Bot',
            'email'            => 'bot@spam.com',
            'message'          => 'Mensaje del bot que rellena el honeypot.',
            'captcha_honeypot' => 'spam',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('contact_messages', ['email' => 'bot@spam.com']);
    }

    public function test_public_contact_throttle_10_per_minute(): void
    {
        $payload = [
            'name'    => 'Juan',
            'email'   => 'juan@ex.com',
            'message' => 'Consulta válida sobre el balneario.',
        ];

        // Las primeras 10 deben pasar.
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/contact', $payload)->assertCreated();
        }

        // La 11° debe ser bloqueada por el throttle.
        $this->postJson('/api/v1/contact', $payload)->assertStatus(429);
    }
}
