<?php

namespace App\Observers;

use App\Models\ProjectTask;
use Illuminate\Support\Facades\Log;

class ProjectTaskObserver
{
    /**
     * Handle the ProjectTask "created" event.
     */
    public function created(ProjectTask $projectTask): void
    {
        $projectTask->update([
            'reference' => $projectTask->project->short_code . '-' . $projectTask->id,
        ]);
    }

    /**
     * Handle the ProjectTask "updated" event.
     */
    public function updated(ProjectTask $projectTask): void
    {
        //
    }

    /**
     * Handle the ProjectTask "deleted" event.
     */
    public function deleted(ProjectTask $projectTask): void
    {
        //
    }

    /**
     * Handle the ProjectTask "restored" event.
     */
    public function restored(ProjectTask $projectTask): void
    {
        //
    }

    /**
     * Handle the ProjectTask "force deleted" event.
     */
    public function forceDeleted(ProjectTask $projectTask): void
    {
        //
    }
}
