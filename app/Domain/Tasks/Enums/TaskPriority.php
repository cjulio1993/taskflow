<?php

declare(strict_types=1);

namespace App\Domain\Tasks\Enums;

enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
