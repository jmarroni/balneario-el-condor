<?php

namespace App\Policies;

use App\Models\UsefulInfo;
use App\Models\User;

class UsefulInfoPolicy
{
    public function viewAny(User $user): bool                       { return $user->can('useful_info.view'); }
    public function view(User $user, UsefulInfo $m): bool           { return $user->can('useful_info.view'); }
    public function create(User $user): bool                        { return $user->can('useful_info.create'); }
    public function update(User $user, UsefulInfo $m): bool         { return $user->can('useful_info.update'); }
    public function delete(User $user, UsefulInfo $m): bool         { return $user->can('useful_info.delete'); }
    public function restore(User $user, UsefulInfo $m): bool        { return $user->can('useful_info.update'); }
}
