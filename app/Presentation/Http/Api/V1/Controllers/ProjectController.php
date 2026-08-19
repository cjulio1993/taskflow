<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Controllers;

use App\Application\Projects\DTOs\CreateProjectData;
use App\Application\Projects\UseCases\CreateProject;
use App\Application\Projects\UseCases\ListProjects;
use App\Presentation\Http\Api\V1\Requests\StoreProjectRequest;
use App\Presentation\Http\Api\V1\Resources\ProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ProjectController
{
    public function index(ListProjects $listProjects): AnonymousResourceCollection
    {
        return ProjectResource::collection($listProjects->execute());
    }

    public function store(StoreProjectRequest $request, CreateProject $createProject): JsonResponse
    {
        $project = $createProject->execute(new CreateProjectData(
            name: $request->string('name')->toString(),
            description: $request->string('description')->toString() ?: null,
        ));

        return (new ProjectResource($project))
            ->response()
            ->setStatusCode(201);
    }
}
