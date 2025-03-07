<?php

namespace App\Providers;

use App\Models\Project;
use App\Models\Invitation;
use App\Models\ProjectTask;
use App\Policies\ProjectPolicy;
use App\Observers\ProjectObserver;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Facades\Gate;
use App\Observers\InvitationObersver;
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

        Project::observe(ProjectObserver::class);
        ProjectTask::observe(ProjectTaskObserver::class);
        Invitation::observe(InvitationObersver::class);
    }
}
