<?php

declare(strict_types=1);

namespace App\Domain\Projects\Repositories;

use App\Domain\Projects\Entities\Project;

interface ProjectRepository
{
    public function save(Project $project): Project;

    /**
     * @return list<Project>
     */
    public function all(): array;
}
