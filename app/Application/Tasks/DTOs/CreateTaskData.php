<?php

declare(strict_types=1);

namespace App\Application\Tasks\DTOs;

use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use DateTimeImmutable;

final readonly class CreateTaskData
{
    public function __construct(
        public int $projectId,
        public int $createdBy,
        public ?int $assignedTo,
        public string $title,
        public ?string $description,
        public TaskStatus $status,
        public TaskPriority $priority,
        public ?DateTimeImmutable $dueDate,
    ) {}
}
