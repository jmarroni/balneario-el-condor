<?php

namespace App\Policies;

use App\Models\EventRegistration;
use App\Models\User;

class EventRegistrationPolicy
{
    public function viewAny(User $user): bool                           { return $user->can('event_registrations.view'); }
    public function view(User $user, EventRegistration $m): bool        { return $user->can('event_registrations.view'); }
    public function create(User $user): bool                            { return $user->can('event_registrations.create'); }
    public function update(User $user, EventRegistration $m): bool      { return $user->can('event_registrations.update'); }
    public function delete(User $user, EventRegistration $m): bool      { return $user->can('event_registrations.delete'); }
    public function restore(User $user, EventRegistration $m): bool     { return $user->can('event_registrations.update'); }
}
