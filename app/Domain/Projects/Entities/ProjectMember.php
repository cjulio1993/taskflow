<?php

declare(strict_types=1);

namespace App\Domain\Projects\Entities;

use App\Domain\Projects\Enums\ProjectMemberRole;

final readonly class ProjectMember
{
    public function __construct(
        private int $userId,
        private string $name,
        private string $email,
        private ProjectMemberRole $role,
    ) {}

    public function userId(): int
    {
        return $this->userId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function role(): ProjectMemberRole
    {
        return $this->role;
    }
}
