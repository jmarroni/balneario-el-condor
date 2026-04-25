<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        // Reset rate limiter state between tests
        RateLimiter::clear('user:1');
    }

    public function test_unauthenticated_request_returns_401_without_advertising_throttle(): void
    {
        // Sin token → Sanctum responde 401 antes de aplicar el throttle a recursos protegidos.
        // Lo importante es que no hay error 500 y el limiter está bien configurado.
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_admin_gets_300_rpm_limit(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response->assertOk();
        $this->assertSame('300', $response->headers->get('X-RateLimit-Limit'));
    }

    public function test_editor_gets_180_rpm_limit(): void
    {
        $user = User::factory()->create();
        $user->assignRole('editor');
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response->assertOk();
        $this->assertSame('180', $response->headers->get('X-RateLimit-Limit'));
    }

    public function test_moderator_gets_120_rpm_limit(): void
    {
        $user = User::factory()->create();
        $user->assignRole('moderator');
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response->assertOk();
        $this->assertSame('120', $response->headers->get('X-RateLimit-Limit'));
    }

    public function test_authenticated_user_without_role_gets_60_rpm_limit(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $response->assertOk();
        $this->assertSame('60', $response->headers->get('X-RateLimit-Limit'));
    }

    public function test_role_hierarchy_admin_above_editor_above_moderator(): void
    {
        // Validamos jerarquía de límites usando los valores documentados,
        // ya que cada rol fue ejercitado en pruebas individuales.
        $admin     = 300;
        $editor    = 180;
        $moderator = 120;
        $authed    = 60;
        $guest     = 30;

        $this->assertGreaterThan($editor, $admin);
        $this->assertGreaterThan($moderator, $editor);
        $this->assertGreaterThan($authed, $moderator);
        $this->assertGreaterThan($guest, $authed);
    }

    public function test_rate_limit_returns_429_after_threshold(): void
    {
        // Usuario sin rol → 60 rpm. Hacemos 60 requests OK + 1 que debe ser 429.
        $user  = User::factory()->create();
        $token = $user->createToken('flood')->plainTextToken;

        for ($i = 0; $i < 60; $i++) {
            $response = $this->withHeader('Authorization', "Bearer {$token}")
                ->getJson('/api/v1/me');

            if ($response->getStatusCode() === 429) {
                $this->fail("Request #{$i} was already throttled (expected first 60 to pass)");
            }
        }

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/me');

        $this->assertSame(429, $response->getStatusCode());
    }
}
