<?php

namespace App\Policies;

use App\Models\NewsletterCampaign;
use App\Models\User;

class NewsletterCampaignPolicy
{
    public function viewAny(User $user): bool                               { return $user->can('newsletter_campaigns.view'); }
    public function view(User $user, NewsletterCampaign $m): bool           { return $user->can('newsletter_campaigns.view'); }
    public function create(User $user): bool                                { return $user->can('newsletter_campaigns.create'); }
    public function update(User $user, NewsletterCampaign $m): bool         { return $user->can('newsletter_campaigns.update'); }
    public function delete(User $user, NewsletterCampaign $m): bool         { return $user->can('newsletter_campaigns.delete'); }
    public function restore(User $user, NewsletterCampaign $m): bool        { return $user->can('newsletter_campaigns.update'); }
}
