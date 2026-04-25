<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    /**
     * Solo admin puede ver la bitácora de actividad. Tasks de auditoría
     * (cambios sobre contenido, usuarios, etc.) son sensibles y no deberían
     * ser visibles para editores o moderadores.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->hasRole('admin');
    }
}
