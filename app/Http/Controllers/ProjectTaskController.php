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
        return Inertia::render('Project/Task/Show', [
            'task' => new ProjectTaskResource($projectTask),
        ]);
    }
}
