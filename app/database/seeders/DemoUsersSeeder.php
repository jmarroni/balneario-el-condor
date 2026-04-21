<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Demo',     'email' => 'admin@balneario.local',     'role' => 'admin'],
            ['name' => 'Editor Demo',    'email' => 'editor@balneario.local',    'role' => 'editor'],
            ['name' => 'Moderator Demo', 'email' => 'moderator@balneario.local', 'role' => 'moderator'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'              => $data['name'],
                    'password'          => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->syncRoles([$data['role']]);
        }
    }
}
