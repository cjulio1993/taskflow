<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Models\User;

final class ProjectPolicy
{
    public function view(User $user, ProjectModel $project): bool
    {
        return $project->members()
            ->whereKey($user->getKey())
            ->exists();
    }

    public function manageMembers(User $user, ProjectModel $project): bool
    {
        return (int) $project->owner_id === (int) $user->getKey();
    }
}
