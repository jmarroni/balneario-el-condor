<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool                           { return $user->can('contact_messages.view'); }
    public function view(User $user, ContactMessage $m): bool           { return $user->can('contact_messages.view'); }
    public function create(User $user): bool                            { return $user->can('contact_messages.create'); }
    public function update(User $user, ContactMessage $m): bool         { return $user->can('contact_messages.update'); }
    public function delete(User $user, ContactMessage $m): bool         { return $user->can('contact_messages.delete'); }
    public function restore(User $user, ContactMessage $m): bool        { return $user->can('contact_messages.update'); }
}
