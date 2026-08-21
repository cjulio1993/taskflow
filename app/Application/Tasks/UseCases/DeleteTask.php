<?php

declare(strict_types=1);

namespace App\Application\Tasks\UseCases;

use App\Domain\Tasks\Repositories\TaskRepository;

final readonly class DeleteTask
{
    public function __construct(private TaskRepository $tasks) {}

    public function execute(int $projectId, int $taskId): void
    {
        $this->tasks->delete($this->tasks->getForProject($projectId, $taskId));
    }
}
