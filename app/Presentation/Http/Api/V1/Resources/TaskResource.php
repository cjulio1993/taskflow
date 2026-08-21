<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Resources;

use App\Domain\Tasks\Entities\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
final class TaskResource extends JsonResource
{
    /** @return array<string, int|string|null> */
    public function toArray(Request $request): array
    {
        /** @var Task $task */
        $task = $this->resource;

        return [
            'id' => $task->id(),
            'project_id' => $task->projectId(),
            'created_by' => $task->createdBy(),
            'assigned_to' => $task->assignedTo(),
            'title' => $task->title(),
            'description' => $task->description(),
            'status' => $task->status()->value,
            'priority' => $task->priority()->value,
            'due_date' => $task->dueDate()?->format('Y-m-d'),
            'created_at' => $task->createdAt()->format(DATE_ATOM),
            'updated_at' => $task->updatedAt()->format(DATE_ATOM),
        ];
    }
}
