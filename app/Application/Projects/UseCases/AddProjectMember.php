<?php

declare(strict_types=1);

namespace App\Application\Projects\UseCases;

use App\Application\Projects\DTOs\AddProjectMemberData;
use App\Domain\Projects\Entities\ProjectMember;
use App\Domain\Projects\Enums\ProjectMemberRole;
use App\Domain\Projects\Exceptions\ProjectMemberAlreadyExists;
use App\Domain\Projects\Repositories\ProjectRepository;

final readonly class AddProjectMember
{
    public function __construct(private ProjectRepository $projects) {}

    public function execute(AddProjectMemberData $data): ProjectMember
    {
        if ($this->projects->isMember($data->projectId, $data->userId)) {
            throw new ProjectMemberAlreadyExists;
        }

        return $this->projects->addMember($data->projectId, new ProjectMember(
            userId: $data->userId,
            name: $data->name,
            email: $data->email,
            role: ProjectMemberRole::Member,
        ));
    }
}
