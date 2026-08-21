<?php

declare(strict_types=1);

namespace App\Application\Projects\UseCases;

use App\Domain\Projects\Entities\ProjectMember;
use App\Domain\Projects\Repositories\ProjectRepository;

final readonly class ListProjectMembers
{
    public function __construct(private ProjectRepository $projects) {}

    /**
     * @return list<ProjectMember>
     */
    public function execute(int $projectId): array
    {
        return $this->projects->membersOf($projectId);
    }
}
