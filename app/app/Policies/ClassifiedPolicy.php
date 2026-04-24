<?php

namespace App\Policies;

use App\Models\Classified;
use App\Models\User;

class ClassifiedPolicy
{
    public function viewAny(User $user): bool                   { return $user->can('classifieds.view'); }
    public function view(User $user, Classified $m): bool       { return $user->can('classifieds.view'); }
    public function create(User $user): bool                    { return $user->can('classifieds.create'); }
    public function update(User $user, Classified $m): bool     { return $user->can('classifieds.update'); }
    public function delete(User $user, Classified $m): bool     { return $user->can('classifieds.delete'); }
    public function restore(User $user, Classified $m): bool    { return $user->can('classifieds.update'); }
}
