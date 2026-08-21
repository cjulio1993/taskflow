<?php

declare(strict_types=1);

namespace App\Domain\Projects\Enums;

enum ProjectMemberRole: string
{
    case Owner = 'owner';
    case Member = 'member';
}
