<?php

declare(strict_types=1);

use App\Presentation\Http\Api\V1\Controllers\AuthController;
use App\Presentation\Http\Api\V1\Controllers\ProjectController;
use App\Presentation\Http\Api\V1\Controllers\ProjectMemberController;
use App\Presentation\Http\Api\V1\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('auth/register', [AuthController::class, 'register'])->name('api.v1.auth.register');
    Route::post('auth/login', [AuthController::class, 'login'])->name('api.v1.auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
        Route::get('me', [AuthController::class, 'me'])->name('api.v1.me');
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('projects', [ProjectController::class, 'index'])->name('api.v1.projects.index');
        Route::post('projects', [ProjectController::class, 'store'])->name('api.v1.projects.store');
        Route::get('projects/{project}', [ProjectController::class, 'show'])
            ->middleware('can:view,project')
            ->name('api.v1.projects.show');

        Route::get('projects/{project}/members', [ProjectMemberController::class, 'index'])
            ->middleware('can:view,project')
            ->name('api.v1.projects.members.index');
        Route::post('projects/{project}/members', [ProjectMemberController::class, 'store'])
            ->middleware('can:manageMembers,project')
            ->name('api.v1.projects.members.store');
        Route::delete('projects/{project}/members/{user}', [ProjectMemberController::class, 'destroy'])
            ->middleware('can:manageMembers,project')
            ->name('api.v1.projects.members.destroy');

        Route::scopeBindings()->group(function (): void {
            Route::get('projects/{project}/tasks', [TaskController::class, 'index'])
                ->middleware('can:view,project')
                ->name('api.v1.projects.tasks.index');
            Route::post('projects/{project}/tasks', [TaskController::class, 'store'])
                ->middleware('can:view,project')
                ->name('api.v1.projects.tasks.store');
            Route::get('projects/{project}/tasks/{task}', [TaskController::class, 'show'])
                ->middleware(['can:view,project', 'can:view,task'])
                ->name('api.v1.projects.tasks.show');
            Route::match(['put', 'patch'], 'projects/{project}/tasks/{task}', [TaskController::class, 'update'])
                ->middleware(['can:view,project', 'can:update,task'])
                ->name('api.v1.projects.tasks.update');
            Route::delete('projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])
                ->middleware(['can:view,project', 'can:delete,task'])
                ->name('api.v1.projects.tasks.destroy');
        });
    });
});
