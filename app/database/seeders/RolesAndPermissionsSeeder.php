<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    private const MODULES = [
        'news', 'news_categories',
        'events', 'event_registrations',
        'lodgings', 'venues', 'rentals',
        'classifieds', 'classified_contacts',
        'service_providers', 'recipes', 'tides',
        'gallery', 'nearby_places', 'useful_info',
        'pages', 'surveys', 'survey_responses',
        'newsletter_subscribers', 'newsletter_campaigns',
        'contact_messages', 'advertising_contacts',
        'users', 'roles',
    ];

    private const ACTIONS = ['view', 'create', 'update', 'delete'];

    private const MODERABLE_MODULES = [
        'classifieds', 'contact_messages',
        'event_registrations', 'newsletter_subscribers',
    ];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        foreach (self::MODULES as $module) {
            foreach (self::ACTIONS as $action) {
                Permission::firstOrCreate([
                    'name'       => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Rol admin: todos los permisos
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // Rol editor: todo menos users.* y roles.*
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorPerms = Permission::where('name', 'not like', 'users.%')
            ->where('name', 'not like', 'roles.%')
            ->get();
        $editor->syncPermissions($editorPerms);

        // Rol moderator: solo view + delete en módulos moderables
        $moderator = Role::firstOrCreate(['name' => 'moderator', 'guard_name' => 'web']);
        $modPerms = collect(self::MODERABLE_MODULES)
            ->flatMap(fn ($m) => ["{$m}.view", "{$m}.delete"])
            ->map(fn ($p) => Permission::where('name', $p)->first())
            ->filter()
            ->all();
        $moderator->syncPermissions($modPerms);
    }
}
