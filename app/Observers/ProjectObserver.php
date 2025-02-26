<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectStatus;
use App\Enums\DefaultProjectStatus;

class ProjectObserver
{
    /**
     * Handle the ProjectTask "created" event.
     */
    public function created(Project $project): void
    {
        foreach (DefaultProjectStatus::cases() as $status) {
            ProjectStatus::create([
                'name'       => $status->value,
                'project_id' => $project->id,
            ]);
        }
    }
}
