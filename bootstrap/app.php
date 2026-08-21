<?php

use App\Domain\Projects\Exceptions\ProjectMemberAlreadyExists;
use App\Domain\Projects\Exceptions\ProjectOwnerCannotBeRemoved;
use App\Domain\Tasks\Exceptions\TaskAssigneeIsNotProjectMember;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ProjectMemberAlreadyExists $exception, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => ['email' => [$exception->getMessage()]],
            ], 422);
        });

        $exceptions->render(function (ProjectOwnerCannotBeRemoved $exception, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => ['user' => [$exception->getMessage()]],
            ], 422);
        });

        $exceptions->render(function (TaskAssigneeIsNotProjectMember $exception, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            return response()->json([
                'message' => $exception->getMessage(),
                'errors' => ['assigned_to' => [$exception->getMessage()]],
            ], 422);
        });
    })->create();
