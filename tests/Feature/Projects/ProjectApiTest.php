<?php

declare(strict_types=1);

use App\Domain\Projects\Entities\Project;
use App\Domain\Projects\Repositories\ProjectRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a project successfully', function (): void {
    $response = $this->postJson('/api/v1/projects', [
        'name' => 'Launch website',
        'description' => 'Prepare the first release.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.name', 'Launch website')
        ->assertJsonPath('data.description', 'Prepare the first release.');

    $this->assertDatabaseHas('projects', [
        'name' => 'Launch website',
        'description' => 'Prepare the first release.',
    ]);
});

it('requires a project name', function (): void {
    $this->postJson('/api/v1/projects', [
        'description' => 'A project without a name.',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('lists projects', function (): void {
    $this->postJson('/api/v1/projects', ['name' => 'First project'])->assertCreated();
    $this->postJson('/api/v1/projects', ['name' => 'Second project'])->assertCreated();

    $this->getJson('/api/v1/projects')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['name' => 'First project'])
        ->assertJsonFragment(['name' => 'Second project']);
});

it('persists and reconstitutes a project through the repository', function (): void {
    $repository = app(ProjectRepository::class);

    $savedProject = $repository->save(Project::create('Repository project', null));
    $projects = $repository->all();

    expect($savedProject->id())->toBeInt()
        ->and($projects)->toHaveCount(1)
        ->and($projects[0]->name())->toBe('Repository project')
        ->and($projects[0]->description())->toBeNull();
});
