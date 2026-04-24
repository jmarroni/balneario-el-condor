<?php

namespace App\Policies;

use App\Models\NearbyPlace;
use App\Models\User;

class NearbyPlacePolicy
{
    public function viewAny(User $user): bool                       { return $user->can('nearby_places.view'); }
    public function view(User $user, NearbyPlace $m): bool          { return $user->can('nearby_places.view'); }
    public function create(User $user): bool                        { return $user->can('nearby_places.create'); }
    public function update(User $user, NearbyPlace $m): bool        { return $user->can('nearby_places.update'); }
    public function delete(User $user, NearbyPlace $m): bool        { return $user->can('nearby_places.delete'); }
    public function restore(User $user, NearbyPlace $m): bool       { return $user->can('nearby_places.update'); }
}
