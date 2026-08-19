<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('registers and authenticates a user', function (): void {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()->assertJsonPath('user.email', 'ada@example.com');
    $this->assertAuthenticated('web');
    $this->assertDatabaseHas('users', ['email' => 'ada@example.com']);
    expect(Hash::check('password123', User::query()->firstOrFail()->password))->toBeTrue();
});

it('validates registration data', function (): void {
    $this->postJson('/api/v1/auth/register', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

it('rejects a duplicate email during registration', function (): void {
    User::factory()->create(['email' => 'ada@example.com']);

    $this->postJson('/api/v1/auth/register', [
        'name' => 'Another Ada',
        'email' => 'ada@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');
});

it('logs in with valid credentials', function (): void {
    $user = User::factory()->create(['password' => 'password123']);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertOk()->assertJsonPath('user.id', $user->id);

    $this->assertAuthenticated('web');
});

it('rejects invalid credentials', function (): void {
    $user = User::factory()->create(['password' => 'password123']);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnprocessable()->assertJsonValidationErrors('email');

    $this->assertGuest('web');
});

it('returns the authenticated user from me', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user, 'web');

    $this->getJson('/api/v1/me')->assertOk()->assertJsonPath('user.id', $user->id);
});

it('rejects an unauthenticated me request', function (): void {
    $this->getJson('/api/v1/me')->assertUnauthorized();
});

it('logs out and invalidates the session', function (): void {
    $user = User::factory()->create(['password' => 'password123']);
    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password123',
    ])->assertOk();

    $this->postJson('/api/v1/auth/logout')->assertOk();
    $this->assertGuest('web');
});

it('protects the authentication endpoint', function (): void {
    $this->getJson('/api/v1/me')->assertUnauthorized();

    $this->actingAs(User::factory()->create(), 'web');

    $this->getJson('/api/v1/me')->assertOk();
});
