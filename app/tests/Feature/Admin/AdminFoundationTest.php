<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_unauthenticated_user_cannot_access_admin(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_authenticated_user_without_role_is_forbidden(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_admin_user_sees_dashboard(): void
    {
        $user = User::factory()->create(['two_factor_confirmed_at' => now()]);
        $user->assignRole('admin');
        $response = $this->actingAs($user)->get('/admin');
        $response->assertOk()->assertSee('Dashboard');
    }

    public function test_user_with_must_reset_password_is_redirected(): void
    {
        $user = User::factory()->create([
            'must_reset_password' => true,
            'two_factor_confirmed_at' => now(),
        ]);
        $user->assignRole('admin');
        $this->actingAs($user)->get('/admin')
            ->assertRedirect(route('password.edit'));
    }

    public function test_dashboard_shows_counts(): void
    {
        $user = User::factory()->create(['two_factor_confirmed_at' => now()]);
        $user->assignRole('admin');
        \App\Models\News::factory()->count(3)->create();
        $response = $this->actingAs($user)->get('/admin');
        $response->assertSee('News')->assertSee('3');
    }
}
