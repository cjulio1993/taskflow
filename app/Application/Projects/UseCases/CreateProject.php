<?php

declare(strict_types=1);

namespace App\Application\Projects\UseCases;

use App\Application\Projects\DTOs\CreateProjectData;
use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Repositories\ProjectRepository;

final readonly class CreateProject
{
    public function __construct(private ProjectRepository $projects) {}

    public function execute(CreateProjectData $data): Project
    {
        $project = Project::create($data->name, $data->description);

        return $this->projects->save($project);
    }
}
