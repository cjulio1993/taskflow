<?php

declare(strict_types=1);

namespace App\Domain\Projects\Repositories;

use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Entities\ProjectMember;

interface ProjectRepository
{
    public function save(Project $project): Project;

    /**
     * @return list<Project>
     */
    public function allForUser(int $userId): array;

    public function get(int $projectId): Project;

    /**
     * @return list<ProjectMember>
     */
    public function membersOf(int $projectId): array;

    public function isMember(int $projectId, int $userId): bool;

    public function addMember(int $projectId, ProjectMember $member): ProjectMember;

    public function removeMember(int $projectId, int $userId): void;
}
