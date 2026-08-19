<?php

declare(strict_types=1);

namespace App\Domain\Projects\Exceptions;

use DomainException;

final class ProjectNameCannotBeEmpty extends DomainException {}
