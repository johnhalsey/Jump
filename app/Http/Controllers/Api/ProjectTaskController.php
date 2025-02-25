<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\Log;
use App\Enums\DefaultProjectStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectTaskResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTaskController extends Controller
{
    public function index(Request $request, Project $project): JsonResource
    {
        return ProjectTaskResource::collection($project->tasks);
    }

    public function update(Request $request, Project $project, ProjectTask $projectTask)
    {
        // update the project here
        $projectTask->update([
            'assignee_id' => $request->input('assignee_id', null),
            'status_id' => $request->input('status_id', $project->statuses()->where('name', DefaultProjectStatus::TO_DO)->first()->id),
        ]);

        return response()->json();
    }
}
