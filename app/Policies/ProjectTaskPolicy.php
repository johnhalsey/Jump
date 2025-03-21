<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Link;
use App\Models\TaskNote;
use App\Models\ProjectTask;

class ProjectTaskPolicy
{
    public function ownNote(User $user, ProjectTask $projectTask, TaskNote $note): bool
    {
        return $note->task->is($projectTask);
    }

    public function ownLink(User $user, ProjectTask $projectTask, Link $link): bool
    {
        return $link->linkable->is($projectTask);
    }
}
