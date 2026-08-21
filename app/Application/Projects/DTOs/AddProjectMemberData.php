<?php

declare(strict_types=1);

namespace App\Application\Projects\DTOs;

final readonly class AddProjectMemberData
{
    public function __construct(
        public int $projectId,
        public int $userId,
        public string $name,
        public string $email,
    ) {}
}
