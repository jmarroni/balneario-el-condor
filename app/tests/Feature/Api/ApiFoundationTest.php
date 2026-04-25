<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->getJson('/api/v1/me')->assertStatus(401);
    }

    public function test_authenticated_with_token_returns_user(): void
    {
        $user  = User::factory()->create()->assignRole('editor');
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.name', $user->name)
            ->assertJsonStructure([
                'data' => ['id', 'name', 'email', 'roles', 'abilities'],
                'meta' => ['version', 'generated_at'],
            ]);

        $this->assertContains('editor', $response->json('data.roles'));
    }

    public function test_invalid_token_returns_401(): void
    {
        $this->withHeader('Authorization', 'Bearer invalid-token-xyz')
            ->getJson('/api/v1/me')
            ->assertStatus(401);
    }

    public function test_me_returns_abilities_from_token(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('limited', ['news:read', 'events:read'])->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me');

        $response->assertOk()
            ->assertJsonPath('data.abilities', ['news:read', 'events:read']);
    }

    public function test_me_includes_meta_version(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('meta-test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('meta.version', 'v1')
            ->assertJsonStructure(['meta' => ['version', 'generated_at']]);
    }
}
