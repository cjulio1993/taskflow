<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Controllers;

use App\Application\Projects\DTOs\AddProjectMemberData;
use App\Application\Projects\UseCases\AddProjectMember;
use App\Application\Projects\UseCases\ListProjectMembers;
use App\Application\Projects\UseCases\RemoveProjectMember;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Models\User;
use App\Presentation\Http\Api\V1\Requests\StoreProjectMemberRequest;
use App\Presentation\Http\Api\V1\Resources\ProjectMemberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class ProjectMemberController
{
    public function index(ProjectModel $project, ListProjectMembers $listProjectMembers): AnonymousResourceCollection
    {
        return ProjectMemberResource::collection($listProjectMembers->execute((int) $project->getKey()));
    }

    public function store(
        StoreProjectMemberRequest $request,
        ProjectModel $project,
        AddProjectMember $addProjectMember,
    ): JsonResponse {
        $member = $request->member();
        $projectMember = $addProjectMember->execute(new AddProjectMemberData(
            projectId: (int) $project->getKey(),
            userId: (int) $member->getKey(),
            name: $member->name,
            email: $member->email,
        ));

        return (new ProjectMemberResource($projectMember))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(ProjectModel $project, User $user, RemoveProjectMember $removeProjectMember): Response
    {
        $removeProjectMember->execute((int) $project->getKey(), (int) $user->getKey());

        return response()->noContent();
    }
}
