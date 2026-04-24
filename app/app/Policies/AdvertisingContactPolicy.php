<?php

namespace App\Policies;

use App\Models\AdvertisingContact;
use App\Models\User;

class AdvertisingContactPolicy
{
    public function viewAny(User $user): bool                               { return $user->can('advertising_contacts.view'); }
    public function view(User $user, AdvertisingContact $m): bool           { return $user->can('advertising_contacts.view'); }
    public function create(User $user): bool                                { return $user->can('advertising_contacts.create'); }
    public function update(User $user, AdvertisingContact $m): bool         { return $user->can('advertising_contacts.update'); }
    public function delete(User $user, AdvertisingContact $m): bool         { return $user->can('advertising_contacts.delete'); }
    public function restore(User $user, AdvertisingContact $m): bool        { return $user->can('advertising_contacts.update'); }
}
