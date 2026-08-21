<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Controllers;

use App\Application\Tasks\DTOs\CreateTaskData;
use App\Application\Tasks\DTOs\UpdateTaskData;
use App\Application\Tasks\UseCases\CreateTask;
use App\Application\Tasks\UseCases\DeleteTask;
use App\Application\Tasks\UseCases\GetProjectTask;
use App\Application\Tasks\UseCases\ListProjectTasks;
use App\Application\Tasks\UseCases\UpdateTask;
use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Infrastructure\Persistence\Eloquent\Models\TaskModel;
use App\Presentation\Http\Api\V1\Requests\StoreTaskRequest;
use App\Presentation\Http\Api\V1\Requests\UpdateTaskRequest;
use App\Presentation\Http\Api\V1\Resources\TaskResource;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class TaskController
{
    public function index(ProjectModel $project, ListProjectTasks $listProjectTasks): AnonymousResourceCollection
    {
        return TaskResource::collection($listProjectTasks->execute((int) $project->getKey()));
    }

    public function store(ProjectModel $project, StoreTaskRequest $request, CreateTask $createTask): JsonResponse
    {
        $data = $request->validated();
        $task = $createTask->execute(new CreateTaskData(
            projectId: (int) $project->getKey(),
            createdBy: (int) $request->user()->getAuthIdentifier(),
            assignedTo: $data['assigned_to'] ?? null,
            title: $data['title'],
            description: $data['description'] ?? null,
            status: TaskStatus::from($data['status']),
            priority: TaskPriority::from($data['priority']),
            dueDate: isset($data['due_date']) ? new DateTimeImmutable($data['due_date']) : null,
        ));

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    public function show(ProjectModel $project, TaskModel $task, GetProjectTask $getProjectTask): TaskResource
    {
        return new TaskResource($getProjectTask->execute((int) $project->getKey(), (int) $task->getKey()));
    }

    public function update(
        ProjectModel $project,
        TaskModel $task,
        UpdateTaskRequest $request,
        UpdateTask $updateTask,
    ): TaskResource {
        $data = $request->validated();
        $updatedTask = $updateTask->execute(new UpdateTaskData(
            projectId: (int) $project->getKey(),
            taskId: (int) $task->getKey(),
            assignedTo: $data['assigned_to'] ?? null,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            status: isset($data['status']) ? TaskStatus::from($data['status']) : null,
            priority: isset($data['priority']) ? TaskPriority::from($data['priority']) : null,
            dueDate: isset($data['due_date']) ? new DateTimeImmutable($data['due_date']) : null,
            fields: array_keys($data),
        ));

        return new TaskResource($updatedTask);
    }

    public function destroy(ProjectModel $project, TaskModel $task, DeleteTask $deleteTask): Response
    {
        $deleteTask->execute((int) $project->getKey(), (int) $task->getKey());

        return response()->noContent();
    }
}
