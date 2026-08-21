<?php

declare(strict_types=1);

namespace App\Domain\Tasks\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';
}
