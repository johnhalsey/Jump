<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectTaskResource;

class ProjectTaskController extends Controller
{
    public function index(Request $request, Project $project): ProjectTaskResource
    {
        return ProjectTaskResource::collection($project->tasks);
    }

    public function update(Request $request, Project $project, ProjectTask $projectTask)
    {
        // update the project here

    }
}
