<?php

declare(strict_types=1);

use App\Application\Projects\DTOs\CreateProjectData;
use App\Application\Projects\UseCases\CreateProject;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createProjectFor(User $owner): ProjectModel
{
    $project = app(CreateProject::class)->execute(new CreateProjectData(
        ownerId: $owner->id,
        name: 'Membership project',
        description: null,
    ));

    return ProjectModel::query()->findOrFail($project->id());
}

it('adds the project creator as the owner and an owner member', function (): void {
    $owner = User::factory()->create();

    $this->actingAs($owner, 'web')->postJson('/api/v1/projects', [
        'name' => 'New project',
    ])->assertCreated();

    $project = ProjectModel::query()->firstOrFail();

    expect((int) $project->owner_id)->toBe($owner->id);
    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $owner->id,
        'role' => 'owner',
    ]);
});

it('lets an owner list members including themselves', function (): void {
    $owner = User::factory()->create();
    $project = createProjectFor($owner);

    $this->actingAs($owner, 'web')->getJson("/api/v1/projects/{$project->id}/members")
        ->assertOk()
        ->assertJsonPath('data.0.id', $owner->id)
        ->assertJsonPath('data.0.role', 'owner');
});

it('lets an owner add an existing user as a member', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = createProjectFor($owner);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/members", [
        'email' => $member->email,
    ])
        ->assertCreated()
        ->assertJsonPath('data.id', $member->id)
        ->assertJsonPath('data.role', 'member');

    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $member->id,
        'role' => 'member',
    ]);
});

it('does not add the same user to a project twice', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = createProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/members", [
        'email' => $member->email,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('does not add an unknown email address', function (): void {
    $owner = User::factory()->create();
    $project = createProjectFor($owner);

    $this->actingAs($owner, 'web')->postJson("/api/v1/projects/{$project->id}/members", [
        'email' => 'missing@example.test',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('email');
});

it('does not let a normal member add project members', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $candidate = User::factory()->create();
    $project = createProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);

    $this->actingAs($member, 'web')->postJson("/api/v1/projects/{$project->id}/members", [
        'email' => $candidate->email,
    ])->assertForbidden();
});

it('does not let an unrelated user manage project members', function (): void {
    $owner = User::factory()->create();
    $unrelatedUser = User::factory()->create();
    $candidate = User::factory()->create();
    $project = createProjectFor($owner);

    $this->actingAs($unrelatedUser, 'web')->getJson("/api/v1/projects/{$project->id}/members")
        ->assertForbidden();
    $this->actingAs($unrelatedUser, 'web')->postJson("/api/v1/projects/{$project->id}/members", [
        'email' => $candidate->email,
    ])->assertForbidden();
    $this->actingAs($unrelatedUser, 'web')->deleteJson("/api/v1/projects/{$project->id}/members/{$owner->id}")
        ->assertForbidden();
});

it('lets an owner remove a normal member', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = createProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);

    $this->actingAs($owner, 'web')->deleteJson("/api/v1/projects/{$project->id}/members/{$member->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('project_user', [
        'project_id' => $project->id,
        'user_id' => $member->id,
    ]);
});

it('does not let a normal member remove another member', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $otherMember = User::factory()->create();
    $project = createProjectFor($owner);
    $project->members()->attach([$member->id => ['role' => 'member'], $otherMember->id => ['role' => 'member']]);

    $this->actingAs($member, 'web')->deleteJson("/api/v1/projects/{$project->id}/members/{$otherMember->id}")
        ->assertForbidden();
});

it('does not let an owner remove themselves', function (): void {
    $owner = User::factory()->create();
    $project = createProjectFor($owner);

    $this->actingAs($owner, 'web')->deleteJson("/api/v1/projects/{$project->id}/members/{$owner->id}")
        ->assertUnprocessable()
        ->assertJsonValidationErrors('user');
});

it('rejects unauthenticated member requests', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = createProjectFor($owner);

    $this->getJson("/api/v1/projects/{$project->id}/members")->assertUnauthorized();
    $this->postJson("/api/v1/projects/{$project->id}/members", ['email' => $member->email])->assertUnauthorized();
    $this->deleteJson("/api/v1/projects/{$project->id}/members/{$member->id}")->assertUnauthorized();
});

it('allows members to view project details', function (): void {
    $owner = User::factory()->create();
    $member = User::factory()->create();
    $project = createProjectFor($owner);
    $project->members()->attach($member, ['role' => 'member']);

    $this->actingAs($member, 'web')->getJson("/api/v1/projects/{$project->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $project->id);
});

it('does not let unrelated users view project details', function (): void {
    $owner = User::factory()->create();
    $unrelatedUser = User::factory()->create();
    $project = createProjectFor($owner);

    $this->actingAs($unrelatedUser, 'web')->getJson("/api/v1/projects/{$project->id}")
        ->assertForbidden();
});
