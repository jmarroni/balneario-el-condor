<?php

namespace App\Policies;

use App\Models\Rental;
use App\Models\User;

class RentalPolicy
{
    public function viewAny(User $user): bool               { return $user->can('rentals.view'); }
    public function view(User $user, Rental $m): bool       { return $user->can('rentals.view'); }
    public function create(User $user): bool                { return $user->can('rentals.create'); }
    public function update(User $user, Rental $m): bool     { return $user->can('rentals.update'); }
    public function delete(User $user, Rental $m): bool     { return $user->can('rentals.delete'); }
    public function restore(User $user, Rental $m): bool    { return $user->can('rentals.update'); }
}
