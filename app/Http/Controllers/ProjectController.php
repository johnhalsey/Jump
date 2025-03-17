<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    public function show(Request $request, Project $project)
    {
        $project->load(['statuses' => function ($query) {
            $query->withCount('tasks');
        }, 'users' => function ($query) use ($project) {
            $query->withCount(['tasks' => function ($query) use ($project) {
                $query->where('project_id', $project->id);
            }]);
        }]);

        return Inertia::render('Project/Show');
    }

}
