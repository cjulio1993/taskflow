<?php

declare(strict_types=1);

namespace App\Domain\Tasks\Repositories;

use App\Domain\Tasks\Entities\Task;

interface TaskRepository
{
    public function save(Task $task): Task;

    /** @return list<Task> */
    public function allForProject(int $projectId): array;

    public function getForProject(int $projectId, int $taskId): Task;

    public function delete(Task $task): void;
}
