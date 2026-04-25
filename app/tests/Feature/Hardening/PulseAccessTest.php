<?php

declare(strict_types=1);

namespace Tests\Feature\Hardening;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PulseAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_pulse_dashboard_redirects_unauth_to_login(): void
    {
        $this->get('/pulse')->assertRedirect('/login');
    }

    public function test_admin_can_access_pulse(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/pulse')
            ->assertOk();
    }

    public function test_editor_cannot_access_pulse(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole('editor');

        $this->actingAs($editor)
            ->get('/pulse')
            ->assertForbidden();
    }

    public function test_moderator_cannot_access_pulse(): void
    {
        $moderator = User::factory()->create();
        $moderator->assignRole('moderator');

        $this->actingAs($moderator)
            ->get('/pulse')
            ->assertForbidden();
    }

    public function test_sentry_dsn_unset_in_test(): void
    {
        $this->assertEmpty(config('sentry.dsn'));
    }
}
