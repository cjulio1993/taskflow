<?php

declare(strict_types=1);

namespace App\Application\Tasks\UseCases;

use App\Domain\Tasks\Entities\Task;
use App\Domain\Tasks\Repositories\TaskRepository;

final readonly class ListProjectTasks
{
    public function __construct(private TaskRepository $tasks) {}

    /** @return list<Task> */
    public function execute(int $projectId): array
    {
        return $this->tasks->allForProject($projectId);
    }
}
