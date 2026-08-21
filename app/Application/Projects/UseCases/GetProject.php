<?php

declare(strict_types=1);

namespace App\Application\Projects\UseCases;

use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Repositories\ProjectRepository;

final readonly class GetProject
{
    public function __construct(private ProjectRepository $projects) {}

    public function execute(int $projectId): Project
    {
        return $this->projects->get($projectId);
    }
}
