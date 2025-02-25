<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\Log;
use App\Enums\DefaultProjectStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectTaskResource;
use App\Http\Requests\UpdateProjectTaskRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTaskController extends Controller
{
    public function index(Request $request, Project $project): JsonResource
    {
        return ProjectTaskResource::collection($project->tasks->sortByDesc('created_at'));
    }

    public function store(Request $request, Project $project): JsonResource
    {
        $task = $project->tasks()->create([
            'status_id'  => $project->statuses()->where('name', DefaultProjectStatus::TO_DO->value)->first()->id,
            'title'      => $request->input('title'),
            'creator_id' => $request->user()->id,
        ]);

        return new ProjectTaskResource($task);
    }

    public function update(UpdateProjectTaskRequest $request, Project $project, ProjectTask $projectTask): JsonResource
    {
        // update the project here
        $projectTask->update([
            'assignee_id' => $request->input('assignee_id', null),
            'status_id'   => $request->input('status_id', $project->statuses()->where('name', DefaultProjectStatus::TO_DO)->first()->id),
        ]);

        return new ProjectTaskResource($projectTask);
    }
}
