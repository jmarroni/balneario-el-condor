<?php

namespace App\Policies;

use App\Models\News;
use App\Models\User;

class NewsPolicy
{
    public function viewAny(User $user): bool   { return $user->can('news.view'); }
    public function view(User $user, News $m): bool    { return $user->can('news.view'); }
    public function create(User $user): bool           { return $user->can('news.create'); }
    public function update(User $user, News $m): bool  { return $user->can('news.update'); }
    public function delete(User $user, News $m): bool  { return $user->can('news.delete'); }
    public function restore(User $user, News $m): bool { return $user->can('news.update'); }
}
