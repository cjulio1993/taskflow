<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Policies;

use App\Infrastructure\Persistence\Eloquent\Models\TaskModel;
use App\Models\User;

final class TaskPolicy
{
    public function view(User $user, TaskModel $task): bool
    {
        return $this->isProjectMember($user, $task);
    }

    public function update(User $user, TaskModel $task): bool
    {
        return $this->isProjectMember($user, $task);
    }

    public function delete(User $user, TaskModel $task): bool
    {
        return $this->isProjectMember($user, $task);
    }

    private function isProjectMember(User $user, TaskModel $task): bool
    {
        return $task->project->members()->whereKey($user->getKey())->exists();
    }
}
