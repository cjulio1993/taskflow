<?php

declare(strict_types=1);

namespace App\Domain\Tasks\Exceptions;

use DomainException;

final class TaskAssigneeIsNotProjectMember extends DomainException
{
    protected $message = 'The assignee must be a member of the project.';
}
