<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

final class ProjectModel extends Model
{
    protected $table = 'projects';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];
}
