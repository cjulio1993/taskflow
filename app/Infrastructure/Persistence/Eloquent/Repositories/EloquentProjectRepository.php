<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Entities\ProjectMember;
use App\Domain\Projects\Enums\ProjectMemberRole;
use App\Domain\Projects\Repositories\ProjectRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class EloquentProjectRepository implements ProjectRepository
{
    public function save(Project $project): Project
    {
        return DB::transaction(function () use ($project): Project {
            $model = $project->id() === null
                ? new ProjectModel
                : ProjectModel::query()->findOrFail($project->id());

            $model->fill([
                'owner_id' => $project->ownerId(),
                'name' => $project->name(),
                'description' => $project->description(),
            ]);
            $model->save();

            $model->members()->syncWithoutDetaching([
                $project->ownerId() => ['role' => ProjectMemberRole::Owner->value],
            ]);

            return $this->toDomain($model);
        });
    }

    public function allForUser(int $userId): array
    {
        return ProjectModel::query()
            ->whereHas('members', fn (Builder $query): Builder => $query->whereKey($userId))
            ->latest('created_at')
            ->latest('id')
            ->get()
            ->map(fn (ProjectModel $project): Project => $this->toDomain($project))
            ->all();
    }

    public function get(int $projectId): Project
    {
        return $this->toDomain(ProjectModel::query()->findOrFail($projectId));
    }

    public function membersOf(int $projectId): array
    {
        return ProjectModel::query()
            ->findOrFail($projectId)
            ->members()
            ->orderBy('users.name')
            ->get()
            ->map(fn (User $user): ProjectMember => new ProjectMember(
                userId: (int) $user->getKey(),
                name: $user->name,
                email: $user->email,
                role: ProjectMemberRole::from($user->pivot->role),
            ))
            ->all();
    }

    public function isMember(int $projectId, int $userId): bool
    {
        return ProjectModel::query()
            ->findOrFail($projectId)
            ->members()
            ->whereKey($userId)
            ->exists();
    }

    public function addMember(int $projectId, ProjectMember $member): ProjectMember
    {
        ProjectModel::query()
            ->findOrFail($projectId)
            ->members()
            ->attach($member->userId(), ['role' => $member->role()->value]);

        return $member;
    }

    public function removeMember(int $projectId, int $userId): void
    {
        ProjectModel::query()
            ->findOrFail($projectId)
            ->members()
            ->detach($userId);
    }

    private function toDomain(ProjectModel $project): Project
    {
        return Project::reconstitute(
            id: (int) $project->getKey(),
            ownerId: (int) $project->owner_id,
            name: $project->name,
            description: $project->description,
            createdAt: $project->created_at->toDateTimeImmutable(),
            updatedAt: $project->updated_at->toDateTimeImmutable(),
        );
    }
}
