<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Resources;

use App\Domain\Projects\Entities\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Project
 */
final class ProjectResource extends JsonResource
{
    /**
     * @return array<string, int|string|null>
     */
    public function toArray(Request $request): array
    {
        /** @var Project $project */
        $project = $this->resource;

        return [
            'id' => $project->id(),
            'name' => $project->name(),
            'description' => $project->description(),
            'created_at' => $project->createdAt()->format(DATE_ATOM),
            'updated_at' => $project->updatedAt()->format(DATE_ATOM),
        ];
    }
}
