<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;

class RecipePolicy
{
    public function viewAny(User $user): bool               { return $user->can('recipes.view'); }
    public function view(User $user, Recipe $m): bool       { return $user->can('recipes.view'); }
    public function create(User $user): bool                { return $user->can('recipes.create'); }
    public function update(User $user, Recipe $m): bool     { return $user->can('recipes.update'); }
    public function delete(User $user, Recipe $m): bool     { return $user->can('recipes.delete'); }
    public function restore(User $user, Recipe $m): bool    { return $user->can('recipes.update'); }
}
