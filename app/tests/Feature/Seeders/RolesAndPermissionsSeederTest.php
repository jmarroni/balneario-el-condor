<?php

namespace Tests\Feature\Seeders;

use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesAndPermissionsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_three_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'editor')->exists());
        $this->assertTrue(Role::where('name', 'moderator')->exists());
    }

    public function test_admin_has_all_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $admin = Role::where('name', 'admin')->first();
        $totalPermissions = Permission::count();

        $this->assertGreaterThan(0, $totalPermissions);
        $this->assertSame($totalPermissions, $admin->permissions()->count());
    }

    public function test_editor_has_content_permissions_but_not_users(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $editor = Role::where('name', 'editor')->first();

        $this->assertTrue($editor->hasPermissionTo('news.create'));
        $this->assertTrue($editor->hasPermissionTo('events.update'));
        $this->assertFalse($editor->hasPermissionTo('users.create'));
        $this->assertFalse($editor->hasPermissionTo('roles.update'));
    }

    public function test_moderator_only_has_view_and_delete_on_moderable_modules(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $mod = Role::where('name', 'moderator')->first();

        $this->assertTrue($mod->hasPermissionTo('classifieds.view'));
        $this->assertTrue($mod->hasPermissionTo('classifieds.delete'));
        $this->assertFalse($mod->hasPermissionTo('classifieds.create'));
        $this->assertFalse($mod->hasPermissionTo('news.view'));
    }
}
