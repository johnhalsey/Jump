<?php

namespace App\Observers;

use App\Models\Project;
use App\Enums\ProjectPlan;
use Illuminate\Support\Str;
use App\Models\ProjectStatus;
use App\Enums\DefaultProjectStatus;

class ProjectObserver
{
    public function creating(Project $project): void
    {
        $project->short_code = Str::upper(Str::random(4));
        $project->plan       = ProjectPlan::FREE;
    }

    /**
     * Handle the ProjectTask "created" event.
     */
    public function created(Project $project): void
    {
        foreach (DefaultProjectStatus::cases() as $status) {
            $project->statuses()->create([
                'name' => $status->value,
            ]);
        }
    }
}
