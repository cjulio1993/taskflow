<?php

declare(strict_types=1);

namespace App\Domain\Tasks\Entities;

use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use DateTimeImmutable;

final readonly class Task
{
    private function __construct(
        private ?int $id,
        private int $projectId,
        private int $createdBy,
        private ?int $assignedTo,
        private string $title,
        private ?string $description,
        private TaskStatus $status,
        private TaskPriority $priority,
        private ?DateTimeImmutable $dueDate,
        private ?DateTimeImmutable $createdAt,
        private ?DateTimeImmutable $updatedAt,
    ) {}

    public static function create(
        int $projectId,
        int $createdBy,
        ?int $assignedTo,
        string $title,
        ?string $description,
        TaskStatus $status,
        TaskPriority $priority,
        ?DateTimeImmutable $dueDate,
    ): self {
        return new self(null, $projectId, $createdBy, $assignedTo, $title, $description, $status, $priority, $dueDate, null, null);
    }

    public static function reconstitute(
        int $id,
        int $projectId,
        int $createdBy,
        ?int $assignedTo,
        string $title,
        ?string $description,
        TaskStatus $status,
        TaskPriority $priority,
        ?DateTimeImmutable $dueDate,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $projectId, $createdBy, $assignedTo, $title, $description, $status, $priority, $dueDate, $createdAt, $updatedAt);
    }

    public function update(
        ?int $assignedTo,
        string $title,
        ?string $description,
        TaskStatus $status,
        TaskPriority $priority,
        ?DateTimeImmutable $dueDate,
    ): self {
        return new self($this->id, $this->projectId, $this->createdBy, $assignedTo, $title, $description, $status, $priority, $dueDate, $this->createdAt, $this->updatedAt);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function projectId(): int
    {
        return $this->projectId;
    }

    public function createdBy(): int
    {
        return $this->createdBy;
    }

    public function assignedTo(): ?int
    {
        return $this->assignedTo;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function status(): TaskStatus
    {
        return $this->status;
    }

    public function priority(): TaskPriority
    {
        return $this->priority;
    }

    public function dueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt ?? new DateTimeImmutable;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt ?? new DateTimeImmutable;
    }
}
