<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Tasks\Entities\Task;
use App\Domain\Tasks\Repositories\TaskRepository;
use App\Infrastructure\Persistence\Eloquent\Models\TaskModel;

final class EloquentTaskRepository implements TaskRepository
{
    public function save(Task $task): Task
    {
        $model = $task->id() === null
            ? new TaskModel
            : TaskModel::query()->findOrFail($task->id());

        $model->fill([
            'project_id' => $task->projectId(),
            'created_by' => $task->createdBy(),
            'assigned_to' => $task->assignedTo(),
            'title' => $task->title(),
            'description' => $task->description(),
            'status' => $task->status(),
            'priority' => $task->priority(),
            'due_date' => $task->dueDate()?->format('Y-m-d'),
        ]);
        $model->save();

        return $this->toDomain($model);
    }

    public function allForProject(int $projectId): array
    {
        return TaskModel::query()
            ->where('project_id', $projectId)
            ->latest('created_at')
            ->latest('id')
            ->get()
            ->map(fn (TaskModel $task): Task => $this->toDomain($task))
            ->all();
    }

    public function getForProject(int $projectId, int $taskId): Task
    {
        return $this->toDomain(TaskModel::query()
            ->where('project_id', $projectId)
            ->findOrFail($taskId));
    }

    public function delete(Task $task): void
    {
        TaskModel::query()->findOrFail($task->id())->delete();
    }

    private function toDomain(TaskModel $task): Task
    {
        return Task::reconstitute(
            id: (int) $task->getKey(),
            projectId: (int) $task->project_id,
            createdBy: (int) $task->created_by,
            assignedTo: $task->assigned_to === null ? null : (int) $task->assigned_to,
            title: $task->title,
            description: $task->description,
            status: $task->status,
            priority: $task->priority,
            dueDate: $task->due_date?->toDateTimeImmutable(),
            createdAt: $task->created_at->toDateTimeImmutable(),
            updatedAt: $task->updated_at->toDateTimeImmutable(),
        );
    }
}
