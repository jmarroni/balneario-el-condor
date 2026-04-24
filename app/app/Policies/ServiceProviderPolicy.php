<?php

namespace App\Policies;

use App\Models\ServiceProvider;
use App\Models\User;

class ServiceProviderPolicy
{
    public function viewAny(User $user): bool                           { return $user->can('service_providers.view'); }
    public function view(User $user, ServiceProvider $m): bool          { return $user->can('service_providers.view'); }
    public function create(User $user): bool                            { return $user->can('service_providers.create'); }
    public function update(User $user, ServiceProvider $m): bool        { return $user->can('service_providers.update'); }
    public function delete(User $user, ServiceProvider $m): bool        { return $user->can('service_providers.delete'); }
    public function restore(User $user, ServiceProvider $m): bool       { return $user->can('service_providers.update'); }
}
