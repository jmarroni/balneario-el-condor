<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Jobs\SendCampaign;
use App\Mail\ContactMessageReceivedMail;
use App\Mail\NewsletterCampaignMail;
use App\Models\NewsCategory;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Cache\RateLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Smoke E2E final de Fase 6.
 *
 * Verifica el flujo end-to-end de la API: crear token → crear recurso → listarlo
 * → consultar detalle → eliminarlo, además del envío de mails (transaccional y
 * de campaña) sobre la misma capa de Sanctum + queues.
 */
class ApiE2ETest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->app->make(RateLimiter::class)->clear('api.v1.contact');
    }

    public function test_complete_flow_token_create_list_show_delete(): void
    {
        Mail::fake();

        // 1. Crear user admin + token.
        $user = User::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('e2e')->plainTextToken;
        $headers = [
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ];

        // 2. /me debería devolver datos del usuario autenticado.
        $this->withHeaders($headers)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonStructure(['data' => ['id', 'name', 'email', 'roles', 'abilities']]);

        // 3. Crear una novedad vía API.
        $category = NewsCategory::factory()->create();
        $create = $this->withHeaders($headers)->postJson('/api/v1/news', [
            'title'            => 'Novedad E2E Fase 6',
            'body'             => 'Cuerpo de prueba suficientemente largo para validación.',
            'news_category_id' => $category->id,
            'published_at'     => now()->toIso8601String(),
        ]);
        $create->assertCreated();
        $slug = $create->json('data.slug');
        $this->assertNotEmpty($slug, 'El slug debería autogenerarse a partir del title');

        // 4. Listar y verificar que aparece la creada (con estructura paginada).
        $this->withHeaders($headers)
            ->getJson('/api/v1/news?per_page=5')
            ->assertOk()
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']])
            ->assertJsonFragment(['slug' => $slug]);

        // 5. Detalle.
        $this->withHeaders($headers)
            ->getJson("/api/v1/news/{$slug}")
            ->assertOk()
            ->assertJsonPath('data.title', 'Novedad E2E Fase 6')
            ->assertJsonPath('data.slug', $slug);

        // 6. Soft-delete vía API.
        $this->withHeaders($headers)
            ->deleteJson("/api/v1/news/{$slug}")
            ->assertNoContent();

        $this->assertSoftDeleted('news', ['slug' => $slug]);
    }

    public function test_public_contact_flow_submits_and_queues_mail(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/v1/contact', [
            'name'    => 'Juan E2E',
            'email'   => 'juan@e2e.test',
            'subject' => 'Smoke E2E',
            'message' => 'Consulta para la API pública en el flujo end-to-end de Fase 6.',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'received_at'], 'meta' => ['message']]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'juan@e2e.test',
            'name'  => 'Juan E2E',
        ]);

        Mail::assertQueued(ContactMessageReceivedMail::class);
    }

    public function test_campaign_end_to_end_dispatches_and_sends(): void
    {
        Mail::fake();

        // 3 confirmados + 1 pending (no debería recibir).
        NewsletterSubscriber::factory()->count(3)->create(['status' => 'confirmed']);
        NewsletterSubscriber::factory()->count(1)->create(['status' => 'pending']);

        $campaign = NewsletterCampaign::factory()->create(['status' => 'draft']);

        (new SendCampaign($campaign))->handle();

        Mail::assertSent(NewsletterCampaignMail::class, 3);

        $fresh = $campaign->fresh();
        $this->assertSame('sent', $fresh->status);
        $this->assertSame(3, $fresh->sent_count);
        $this->assertNotNull($fresh->sent_at);
    }
}
