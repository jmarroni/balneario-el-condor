<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dashboard');
    }

    public function test_editor_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('editor');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_moderator_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('moderator');

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk();
    }

    public function test_user_without_any_role_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }
}
