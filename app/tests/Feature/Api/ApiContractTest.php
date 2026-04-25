<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Mail\AdvertisingContactReceivedMail;
use App\Mail\ClassifiedContactMail;
use App\Mail\ContactMessageReceivedMail;
use App\Mail\EventRegistrationConfirmationMail;
use App\Mail\NewsletterConfirmMail;
use App\Mail\NewsletterUnsubscribedMail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Cross-cutting contract tests.
 *
 * Estos tests verifican garantías que se aplican a TODA la superficie de la API
 * y a la familia completa de Mailables — fallan si alguien rompe el contrato
 * global (auth, async delivery, envelope de respuesta).
 */
class ApiContractTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Rutas /api/v1/* públicas que NO deben estar tras `auth:sanctum`.
     *
     * Si crece la superficie pública (formulario embebido, webhooks, etc.) hay
     * que sumarlas explícitamente acá — la lista actúa como allow-list.
     *
     * @var list<string>
     */
    private const PUBLIC_API_URIS = [
        'api/v1/contact',
    ];

    public function test_all_api_v1_routes_require_auth_except_whitelisted_public(): void
    {
        // Laravel guarda el middleware tanto como alias corto (`auth:sanctum`) como
        // FQCN (`Illuminate\Auth\Middleware\Authenticate:sanctum`) según el contexto.
        // Aceptamos ambas formas.
        $authAliases = [
            'auth:sanctum',
            Authenticate::class.':sanctum',
        ];

        $apiRoutes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($r) => str_starts_with($r->uri(), 'api/v1'));

        $this->assertGreaterThan(0, $apiRoutes->count(), 'Debe haber rutas /api/v1/* registradas');

        foreach ($apiRoutes as $route) {
            $uri = $route->uri();
            $method = $route->methods()[0];
            $middlewares = $route->gatherMiddleware();

            $hasAuth = (bool) array_intersect($authAliases, $middlewares);

            if (in_array($uri, self::PUBLIC_API_URIS, true)) {
                $this->assertFalse(
                    $hasAuth,
                    "Ruta pública {$method} /{$uri} no debería requerir auth:sanctum"
                );

                continue;
            }

            $this->assertTrue(
                $hasAuth,
                "Ruta API {$method} /{$uri} debería requerir auth:sanctum (middlewares: ".implode(',', $middlewares).')'
            );
        }
    }

    public function test_all_transactional_mailables_implement_should_queue(): void
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

        foreach ($mailables as $cls) {
            $interfaces = class_implements($cls) ?: [];
            $this->assertArrayHasKey(
                ShouldQueue::class,
                $interfaces,
                "{$cls} debería implementar ShouldQueue para envío asíncrono vía Redis"
            );
        }
    }

    public function test_api_response_envelope_has_data_and_meta(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('contract')->plainTextToken;

        $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])
            ->getJson('/api/v1/news')
            ->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => ['total', 'per_page', 'current_page', 'last_page'],
            ]);
    }
}
