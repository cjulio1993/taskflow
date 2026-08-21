<?php

namespace App\Providers;

use App\Domain\Projects\Repositories\ProjectRepository;
use App\Domain\Tasks\Repositories\TaskRepository;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Infrastructure\Persistence\Eloquent\Models\TaskModel;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProjectRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentTaskRepository;
use App\Presentation\Http\Api\V1\Policies\ProjectPolicy;
use App\Presentation\Http\Api\V1\Policies\TaskPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectRepository::class, EloquentProjectRepository::class);
        $this->app->bind(TaskRepository::class, EloquentTaskRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ProjectModel::class, ProjectPolicy::class);
        Gate::policy(TaskModel::class, TaskPolicy::class);
    }
}
