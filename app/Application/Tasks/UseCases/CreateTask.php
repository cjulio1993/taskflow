<?php

declare(strict_types=1);

namespace App\Application\Tasks\UseCases;

use App\Application\Tasks\DTOs\CreateTaskData;
use App\Domain\Projects\Repositories\ProjectRepository;
use App\Domain\Tasks\Entities\Task;
use App\Domain\Tasks\Exceptions\TaskAssigneeIsNotProjectMember;
use App\Domain\Tasks\Repositories\TaskRepository;

final readonly class CreateTask
{
    public function __construct(private TaskRepository $tasks, private ProjectRepository $projects) {}

    public function execute(CreateTaskData $data): Task
    {
        if ($data->assignedTo !== null && ! $this->projects->isMember($data->projectId, $data->assignedTo)) {
            throw new TaskAssigneeIsNotProjectMember;
        }

        return $this->tasks->save(Task::create(
            $data->projectId, $data->createdBy, $data->assignedTo, $data->title, $data->description,
            $data->status, $data->priority, $data->dueDate,
        ));
    }
}
