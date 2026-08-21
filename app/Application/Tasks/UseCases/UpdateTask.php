<?php

declare(strict_types=1);

namespace App\Application\Tasks\UseCases;

use App\Application\Tasks\DTOs\UpdateTaskData;
use App\Domain\Projects\Repositories\ProjectRepository;
use App\Domain\Tasks\Entities\Task;
use App\Domain\Tasks\Exceptions\TaskAssigneeIsNotProjectMember;
use App\Domain\Tasks\Repositories\TaskRepository;

final readonly class UpdateTask
{
    public function __construct(private TaskRepository $tasks, private ProjectRepository $projects) {}

    public function execute(UpdateTaskData $data): Task
    {
        $task = $this->tasks->getForProject($data->projectId, $data->taskId);
        $assignedTo = $data->has('assigned_to') ? $data->assignedTo : $task->assignedTo();

        if ($assignedTo !== null && ! $this->projects->isMember($data->projectId, $assignedTo)) {
            throw new TaskAssigneeIsNotProjectMember;
        }

        return $this->tasks->save($task->update(
            $assignedTo,
            $data->has('title') ? $data->title : $task->title(),
            $data->has('description') ? $data->description : $task->description(),
            $data->has('status') ? $data->status : $task->status(),
            $data->has('priority') ? $data->priority : $task->priority(),
            $data->has('due_date') ? $data->dueDate : $task->dueDate(),
        ));
    }
}
