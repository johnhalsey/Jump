<?php

namespace App\Http\Controllers\Project;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectTaskResource;

class TaskController extends Controller
{
    public function show(Request $request, Project $project, ProjectTask $projectTask): Response
    {
        $project->load(['statuses', 'users']);

        return Inertia::render('Project/Task/Show', [
            'task' => new ProjectTaskResource($projectTask->load(['notes', 'notes.user', 'project', 'status', 'assignee'])),
        ]);
    }
}
