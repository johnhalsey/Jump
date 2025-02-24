<?php

namespace App\Providers;

use App\Models\ProjectTask;
use App\Policies\ProjectPolicy;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Gate;
use App\Observers\ProjectTaskObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Gate::define('view', [ProjectPolicy::class, 'view']);

        ProjectTask::observe(ProjectTaskObserver::class);
    }
}
