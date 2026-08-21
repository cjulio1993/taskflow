<?php

declare(strict_types=1);

namespace App\Application\Projects\UseCases;

use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Repositories\ProjectRepository;

final readonly class ListProjects
{
    public function __construct(private ProjectRepository $projects) {}

    /**
     * @return list<Project>
     */
    public function execute(int $userId): array
    {
        return $this->projects->allForUser($userId);
    }
}
