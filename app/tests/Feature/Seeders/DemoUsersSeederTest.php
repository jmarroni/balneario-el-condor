<?php

namespace Tests\Feature\Seeders;

use App\Models\User;
use Database\Seeders\DemoUsersSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoUsersSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_one_user_per_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(DemoUsersSeeder::class);

        $admin = User::where('email', 'admin@balneario.local')->first();
        $editor = User::where('email', 'editor@balneario.local')->first();
        $moderator = User::where('email', 'moderator@balneario.local')->first();

        $this->assertNotNull($admin);
        $this->assertNotNull($editor);
        $this->assertNotNull($moderator);

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($editor->hasRole('editor'));
        $this->assertTrue($moderator->hasRole('moderator'));
    }

    public function test_demo_passwords_are_hashed_and_match_known_value(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(DemoUsersSeeder::class);

        $admin = User::where('email', 'admin@balneario.local')->first();
        $this->assertTrue(Hash::check('password', $admin->password));
    }
}
