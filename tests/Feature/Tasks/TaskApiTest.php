<?php

declare(strict_types=1);

use App\Application\Projects\DTOs\CreateProjectData;
use App\Application\Projects\UseCases\CreateProject;
use App\Domain\Tasks\Enums\TaskPriority;
use App\Domain\Tasks\Enums\TaskStatus;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Infrastructure\Persistence\Eloquent\Models\TaskModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function taskProjectFor(User $owner): ProjectModel
{
    $project = app(CreateProject::class)->execute(new CreateProjectData(
        ownerId: $owner->id,
        name: 'Task project',
        description: null,
    ));

    return ProjectModel::query()->findOrFail($project->id());
}

function taskFor(ProjectModel $project, User $creator, ?User $assignee = null): TaskModel
{
    return TaskModel::query()->create([
        'project_id' => $project->id,
        'created_by' => $creator->id,
        'assigned_to' => $assignee?->id,
        'title' => 'Existing task',
        'description' => null,
        'status' => TaskStatus::Todo,
        'priority' => TaskPriority::Medium,
        'due_date' => null,
    ]);
}

function taskPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Implement authentication',
        'description' => 'Add secure SPA authentication.',
        'status' => 'todo',
        'priority' => 'high',
        'due_date' => '2026-08-25',
    ], $overrides);
}

it('lets a project member list tasks', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = taskProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);
    taskFor($project, $owner);

    $this->actingAs($member, 'web')->getJson("/api/v1/projects/{$project->id}/tasks")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Existing task');
});

it('does not let an unrelated user list tasks', function (): void {
    $owner = User::factory()->create();
    $unrelatedUser = User::factory()->create();
    $project = taskProjectFor($owner);

    $this->actingAs($unrelatedUser, 'web')->getJson("/api/v1/projects/{$project->id}/tasks")
        ->assertForbidden();
});

it('lets a project member create a task and sets the creator', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = taskProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);

    $this->actingAs($member, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload())
        ->assertCreated()
        ->assertJsonPath('data.created_by', $member->id)
        ->assertJsonPath('data.status', 'todo');

    $this->assertDatabaseHas('tasks', [
        'project_id' => $project->id,
        'created_by' => $member->id,
        'title' => 'Implement authentication',
    ]);
});

it('does not accept project or creator identifiers from the task payload', function (): void {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = taskProjectFor($owner);
    $otherProject = app(CreateProject::class)->execute(new CreateProjectData(
        ownerId: $otherUser->id,
        name: 'Other project',
        description: null,
    ));

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload([
        'project_id' => $otherProject->id(),
        'created_by' => $otherUser->id,
    ]))
        ->assertCreated()
        ->assertJsonPath('data.project_id', $project->id)
        ->assertJsonPath('data.created_by', $owner->id);
});

it('creates a task without an assignee', function (): void {
    $owner = User::factory()->create();
    $project = taskProjectFor($owner);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload())
        ->assertCreated()
        ->assertJsonPath('data.assigned_to', null);
});

it('assigns a task to a project member', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = taskProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload([
        'assigned_to' => $member->id,
    ]))
        ->assertCreated()
        ->assertJsonPath('data.assigned_to', $member->id);
});

it('does not assign a task to an unrelated user', function (): void {
    $owner = User::factory()->create();
    $unrelatedUser = User::factory()->create();
    $project = taskProjectFor($owner);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload([
        'assigned_to' => $unrelatedUser->id,
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('assigned_to');
});

it('rejects invalid task status and priority values', function (): void {
    $owner = User::factory()->create();
    $project = taskProjectFor($owner);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload([
        'status' => 'blocked',
        'priority' => 'urgent',
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status', 'priority']);
});

it('rejects a title longer than 255 characters', function (): void {
    $owner = User::factory()->create();
    $project = taskProjectFor($owner);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload([
        'title' => str_repeat('a', 256),
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('title');
});

it('lets a project member view a task', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = taskProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);
    $task = taskFor($project, $owner);

    $this->actingAs($member, 'web')->getJson("/api/v1/projects/{$project->id}/tasks/{$task->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $task->id);
});

it('does not let an unrelated user view a task', function (): void {
    $owner = User::factory()->create();
    $unrelatedUser = User::factory()->create();
    $project = taskProjectFor($owner);
    $task = taskFor($project, $owner);

    $this->actingAs($unrelatedUser, 'web')->getJson("/api/v1/projects/{$project->id}/tasks/{$task->id}")
        ->assertForbidden();
});

it('lets a project member update a task and change its status', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = taskProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);
    $task = taskFor($project, $owner);

    $this->actingAs($member, 'web')->patchJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
        'title' => 'Updated task',
        'status' => 'done',
    ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated task')
        ->assertJsonPath('data.status', 'done');
});

it('lets a project member assign and reassign a task', function (): void {
    $owner = User::factory()->create();
    $firstMember = User::factory()->create();
    $secondMember = User::factory()->create();
    $project = taskProjectFor($owner);
    $project->members()->attach([
        $firstMember->id => ['role' => 'member'],
        $secondMember->id => ['role' => 'member'],
    ]);
    $task = taskFor($project, $owner);

    $this->actingAs($owner, 'web')->patchJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
        'assigned_to' => $firstMember->id,
    ])->assertOk()->assertJsonPath('data.assigned_to', $firstMember->id);

    $this->actingAs($owner, 'web')->patchJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", [
        'assigned_to' => $secondMember->id,
    ])->assertOk()->assertJsonPath('data.assigned_to', $secondMember->id);
});

it('lets a project member delete a task', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = taskProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);
    $task = taskFor($project, $owner);

    $this->actingAs($member, 'web')->deleteJson("/api/v1/projects/{$project->id}/tasks/{$task->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

it('does not let an unrelated user create, update, or delete tasks', function (): void {
    $owner = User::factory()->create();
    $unrelatedUser = User::factory()->create();
    $project = taskProjectFor($owner);
    $task = taskFor($project, $owner);

    $this->actingAs($unrelatedUser, 'web')->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload())
        ->assertForbidden();
    $this->actingAs($unrelatedUser, 'web')->patchJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", ['title' => 'No'])
        ->assertForbidden();
    $this->actingAs($unrelatedUser, 'web')->deleteJson("/api/v1/projects/{$project->id}/tasks/{$task->id}")
        ->assertForbidden();
});

it('rejects unauthenticated task requests', function (): void {
    $owner = User::factory()->create();
    $project = taskProjectFor($owner);
    $task = taskFor($project, $owner);

    $this->getJson("/api/v1/projects/{$project->id}/tasks")->assertUnauthorized();
    $this->postJson("/api/v1/projects/{$project->id}/tasks", taskPayload())->assertUnauthorized();
    $this->patchJson("/api/v1/projects/{$project->id}/tasks/{$task->id}", ['title' => 'No'])->assertUnauthorized();
    $this->deleteJson("/api/v1/projects/{$project->id}/tasks/{$task->id}")->assertUnauthorized();
});

it('does not expose a task through a different project route', function (): void {
    $owner = User::factory()->create();
    $firstProject = taskProjectFor($owner);
    $secondProject = app(CreateProject::class)->execute(new CreateProjectData(
        ownerId: $owner->id,
        name: 'Second project',
        description: null,
    ));
    $task = taskFor($firstProject, $owner);

    $this->actingAs($owner, 'web')->getJson("/api/v1/projects/{$secondProject->id()}/tasks/{$task->id}")
        ->assertNotFound();
});
