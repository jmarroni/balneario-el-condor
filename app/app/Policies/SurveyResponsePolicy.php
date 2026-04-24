<?php

namespace App\Policies;

use App\Models\SurveyResponse;
use App\Models\User;

class SurveyResponsePolicy
{
    public function viewAny(User $user): bool                           { return $user->can('survey_responses.view'); }
    public function view(User $user, SurveyResponse $m): bool           { return $user->can('survey_responses.view'); }
    public function create(User $user): bool                            { return $user->can('survey_responses.create'); }
    public function update(User $user, SurveyResponse $m): bool         { return $user->can('survey_responses.update'); }
    public function delete(User $user, SurveyResponse $m): bool         { return $user->can('survey_responses.delete'); }
    public function restore(User $user, SurveyResponse $m): bool        { return $user->can('survey_responses.update'); }
}
