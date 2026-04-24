<?php

namespace App\Policies;

use App\Models\Tide;
use App\Models\User;

class TidePolicy
{
    public function viewAny(User $user): bool               { return $user->can('tides.view'); }
    public function view(User $user, Tide $m): bool         { return $user->can('tides.view'); }
    public function create(User $user): bool                { return $user->can('tides.create'); }
    public function update(User $user, Tide $m): bool       { return $user->can('tides.update'); }
    public function delete(User $user, Tide $m): bool       { return $user->can('tides.delete'); }
    public function restore(User $user, Tide $m): bool      { return $user->can('tides.update'); }
}
