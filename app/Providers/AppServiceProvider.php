<?php

namespace App\Providers;

use App\Domain\Projects\Repositories\ProjectRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProjectRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ProjectRepository::class, EloquentProjectRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
