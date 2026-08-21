<?php

declare(strict_types=1);

namespace App\Application\Projects\UseCases;

use App\Domain\Projects\Exceptions\ProjectOwnerCannotBeRemoved;
use App\Domain\Projects\Repositories\ProjectRepository;

final readonly class RemoveProjectMember
{
    public function __construct(private ProjectRepository $projects) {}

    public function execute(int $projectId, int $userId): void
    {
        $project = $this->projects->get($projectId);

        if ($project->ownerId() === $userId) {
            throw new ProjectOwnerCannotBeRemoved;
        }

        $this->projects->removeMember($projectId, $userId);
    }
}
