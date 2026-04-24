<?php

namespace App\Console\Commands\Etl;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EtlUsersCommand extends BaseEtlCommand
{
    protected $signature   = 'etl:users';
    protected $description = 'Migra usuarios legacy → users + model_has_roles';

    protected string $legacyTable = 'usuarios';
    protected string $targetModel = User::class;

    protected function mapRow(object $row): ?array
    {
        $email = $this->validEmail($row->us_email);
        if ($email === null) {
            return null; // legacy tiene users con emails vacíos
        }

        return [
            'legacy_id'          => $row->us_id,
            'name'               => trim($this->toUtf8($row->us_nombre) . ' ' . $this->toUtf8($row->us_apellido)) ?: $this->toUtf8($row->us_nick),
            'email'              => $email,
            // SHA1 del legacy no se puede rehashear a bcrypt → forzar reset en primer login
            'password'           => Hash::make(bin2hex(random_bytes(16))),
            'email_verified_at'  => null,
            'must_reset_password' => true,
        ];
    }

    protected function afterUpsert($user, object $row): void
    {
        $legacyRole = strtolower(trim($this->toUtf8($row->us_rol ?? '')));
        // Mapeo rudimentario; ajustar según valores reales
        $role = match ($legacyRole) {
            'admin', 'administrador' => 'admin',
            'editor', 'redactor'     => 'editor',
            default                  => 'moderator',
        };

        if (Role::where('name', $role)->exists()) {
            $user->syncRoles([$role]);
        }
    }
}
