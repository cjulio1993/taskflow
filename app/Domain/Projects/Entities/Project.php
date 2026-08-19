<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\Exceptions\ProjectNameCannotBeEmpty;
use DateTimeImmutable;

final class Project
{
    private function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly ?string $description,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
    ) {}

    public static function create(string $name, ?string $description): self
    {
        $name = trim($name);

        if ($name === '') {
            throw new ProjectNameCannotBeEmpty;
        }

        $now = new DateTimeImmutable;

        return new self(
            id: null,
            name: $name,
            description: self::normalizeDescription($description),
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public static function reconstitute(
        int $id,
        string $name,
        ?string $description,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            id: $id,
            name: $name,
            description: $description,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private static function normalizeDescription(?string $description): ?string
    {
        if ($description === null) {
            return null;
        }

        $description = trim($description);

        return $description === '' ? null : $description;
    }
}
