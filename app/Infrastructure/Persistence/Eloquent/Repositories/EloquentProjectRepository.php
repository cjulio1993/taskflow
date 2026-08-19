<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Repositories\ProjectRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;

final class EloquentProjectRepository implements ProjectRepository
{
    public function save(Project $project): Project
    {
        $model = $project->id() === null
            ? new ProjectModel
            : ProjectModel::query()->findOrFail($project->id());

        $model->fill([
            'name' => $project->name(),
            'description' => $project->description(),
        ]);
        $model->save();

        return $this->toDomain($model);
    }

    public function all(): array
    {
        return ProjectModel::query()
            ->latest('created_at')
            ->latest('id')
            ->get()
            ->map(fn (ProjectModel $project): Project => $this->toDomain($project))
            ->all();
    }

    private function toDomain(ProjectModel $project): Project
    {
        return Project::reconstitute(
            id: (int) $project->getKey(),
            name: $project->name,
            description: $project->description,
            createdAt: $project->created_at->toDateTimeImmutable(),
            updatedAt: $project->updated_at->toDateTimeImmutable(),
        );
    }
}
