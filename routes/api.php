<?php

declare(strict_types=1);

use App\Presentation\Http\Api\V1\Controllers\AuthController;
use App\Presentation\Http\Api\V1\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware('web')->group(function (): void {
        Route::post('auth/register', [AuthController::class, 'register'])->name('api.v1.auth.register');
        Route::post('auth/login', [AuthController::class, 'login'])->name('api.v1.auth.login');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
            Route::get('me', [AuthController::class, 'me'])->name('api.v1.me');
        });
    });

    Route::get('projects', [ProjectController::class, 'index'])->name('api.v1.projects.index');
    Route::post('projects', [ProjectController::class, 'store'])->name('api.v1.projects.store');
});
