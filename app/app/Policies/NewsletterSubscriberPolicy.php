<?php

namespace App\Policies;

use App\Models\NewsletterSubscriber;
use App\Models\User;

class NewsletterSubscriberPolicy
{
    public function viewAny(User $user): bool                               { return $user->can('newsletter_subscribers.view'); }
    public function view(User $user, NewsletterSubscriber $m): bool         { return $user->can('newsletter_subscribers.view'); }
    public function create(User $user): bool                                { return $user->can('newsletter_subscribers.create'); }
    public function update(User $user, NewsletterSubscriber $m): bool       { return $user->can('newsletter_subscribers.update'); }
    public function delete(User $user, NewsletterSubscriber $m): bool       { return $user->can('newsletter_subscribers.delete'); }
    public function restore(User $user, NewsletterSubscriber $m): bool      { return $user->can('newsletter_subscribers.update'); }
}
