<?php

declare(strict_types=1);

namespace App\Application\Tasks\UseCases;

use App\Domain\Tasks\Entities\Task;
use App\Domain\Tasks\Repositories\TaskRepository;

final readonly class GetProjectTask
{
    public function __construct(private TaskRepository $tasks) {}

    public function execute(int $projectId, int $taskId): Task
    {
        return $this->tasks->getForProject($projectId, $taskId);
    }
}
