<?php

namespace App\Policies;

use App\Models\ClassifiedContact;
use App\Models\User;

class ClassifiedContactPolicy
{
    public function viewAny(User $user): bool                           { return $user->can('classified_contacts.view'); }
    public function view(User $user, ClassifiedContact $m): bool        { return $user->can('classified_contacts.view'); }
    public function create(User $user): bool                            { return $user->can('classified_contacts.create'); }
    public function update(User $user, ClassifiedContact $m): bool      { return $user->can('classified_contacts.update'); }
    public function delete(User $user, ClassifiedContact $m): bool      { return $user->can('classified_contacts.delete'); }
    public function restore(User $user, ClassifiedContact $m): bool     { return $user->can('classified_contacts.update'); }
}
