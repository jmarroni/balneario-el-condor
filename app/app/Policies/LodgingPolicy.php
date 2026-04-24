<?php

namespace App\Policies;

use App\Models\Lodging;
use App\Models\User;

class LodgingPolicy
{
    public function viewAny(User $user): bool               { return $user->can('lodgings.view'); }
    public function view(User $user, Lodging $m): bool      { return $user->can('lodgings.view'); }
    public function create(User $user): bool                { return $user->can('lodgings.create'); }
    public function update(User $user, Lodging $m): bool    { return $user->can('lodgings.update'); }
    public function delete(User $user, Lodging $m): bool    { return $user->can('lodgings.delete'); }
    public function restore(User $user, Lodging $m): bool   { return $user->can('lodgings.update'); }
}
