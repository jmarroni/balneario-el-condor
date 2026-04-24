<?php

namespace App\Policies;

use App\Models\NewsCategory;
use App\Models\User;

class NewsCategoryPolicy
{
    public function viewAny(User $user): bool                       { return $user->can('news_categories.view'); }
    public function view(User $user, NewsCategory $m): bool         { return $user->can('news_categories.view'); }
    public function create(User $user): bool                        { return $user->can('news_categories.create'); }
    public function update(User $user, NewsCategory $m): bool       { return $user->can('news_categories.update'); }
    public function delete(User $user, NewsCategory $m): bool       { return $user->can('news_categories.delete'); }
    public function restore(User $user, NewsCategory $m): bool      { return $user->can('news_categories.update'); }
}
