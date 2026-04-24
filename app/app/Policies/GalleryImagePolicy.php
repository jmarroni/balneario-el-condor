<?php

namespace App\Policies;

use App\Models\GalleryImage;
use App\Models\User;

class GalleryImagePolicy
{
    public function viewAny(User $user): bool                       { return $user->can('gallery.view'); }
    public function view(User $user, GalleryImage $m): bool         { return $user->can('gallery.view'); }
    public function create(User $user): bool                        { return $user->can('gallery.create'); }
    public function update(User $user, GalleryImage $m): bool       { return $user->can('gallery.update'); }
    public function delete(User $user, GalleryImage $m): bool       { return $user->can('gallery.delete'); }
    public function restore(User $user, GalleryImage $m): bool      { return $user->can('gallery.update'); }
}
