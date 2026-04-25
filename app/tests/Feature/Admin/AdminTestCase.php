<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $editor;
    protected User $moderator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        // Por default todos los admin tests asumen 2FA ya confirmado para
        // saltear el middleware require.2fa.admin. Los tests específicos de
        // 2FA crean su propio usuario con factory().
        $this->admin     = User::factory()->create(['two_factor_confirmed_at' => now()])->assignRole('admin');
        $this->editor    = User::factory()->create()->assignRole('editor');
        $this->moderator = User::factory()->create()->assignRole('moderator');
    }

    protected function asAdmin(): self     { $this->actingAs($this->admin); return $this; }
    protected function asEditor(): self    { $this->actingAs($this->editor); return $this; }
    protected function asModerator(): self { $this->actingAs($this->moderator); return $this; }
    protected function asGuest(User $u): self { $this->actingAs(User::factory()->create()); return $this; }
}
