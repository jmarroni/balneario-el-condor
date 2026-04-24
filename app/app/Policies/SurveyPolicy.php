<?php

namespace App\Policies;

use App\Models\Survey;
use App\Models\User;

class SurveyPolicy
{
    public function viewAny(User $user): bool               { return $user->can('surveys.view'); }
    public function view(User $user, Survey $m): bool       { return $user->can('surveys.view'); }
    public function create(User $user): bool                { return $user->can('surveys.create'); }
    public function update(User $user, Survey $m): bool     { return $user->can('surveys.update'); }
    public function delete(User $user, Survey $m): bool     { return $user->can('surveys.delete'); }
    public function restore(User $user, Survey $m): bool    { return $user->can('surveys.update'); }
}
