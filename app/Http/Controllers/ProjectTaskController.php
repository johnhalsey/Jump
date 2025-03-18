<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectTaskResource;

class ProjectTaskController extends Controller
{
    public function show(Request $request, Project $project, ProjectTask $projectTask)
    {
        $project->load(['statuses', 'users']);

        return Inertia::render('Project/Task/Show', [
            'task' => new ProjectTaskResource($projectTask->load(['notes', 'notes.user', 'project', 'status', 'assignee'])),
        ]);
    }
}
