<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Support\Facades\Log;
use App\Enums\DefaultProjectStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectTaskResource;
use App\Http\Requests\StoreProjectTaskRequest;
use App\Http\Requests\UpdateProjectTaskRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectTaskController extends Controller
{
    public function index(Request $request, Project $project): JsonResource
    {
        if ($request->has('search') && !empty($request->get('search'))) {
            $query = $project->tasks()->with('project')->where('title', 'like', '%' . $request->get('search') . '%')
                ->orWhere('reference', 'like', '%' . $request->get('search') . '%');
        } else {
            $query = $project->tasks()->with('project');
        }

        return ProjectTaskResource::collection($query->orderByDesc('created_at')->get());
    }

    public function store(StoreProjectTaskRequest $request, Project $project): JsonResource
    {
        $task = $project->tasks()->create([
            'status_id'  => $project->statuses()->where('name', DefaultProjectStatus::TO_DO->value)->first()->id,
            'title'      => $request->input('title'),
            'creator_id' => $request->user()->id,
        ]);

        return new ProjectTaskResource($task->load('project'));
    }

    public function update(UpdateProjectTaskRequest $request, Project $project, ProjectTask $projectTask): JsonResource
    {
        // update the project here
        $projectTask->update([
            'assignee_id' => $request->input('assignee_id', $projectTask->assignee_id),
            'status_id'   => $request->input('status_id', $projectTask->status_id),
            'description' => $request->input('description', $projectTask->description),
            'title'       => $request->input('title', $projectTask->title),
        ]);

        return new ProjectTaskResource($projectTask->load('project'));
    }

    public function destroy(Request $request, Project $project, ProjectTask $projectTask)
    {
        $projectTask->delete();

        return response()->noContent();
    }
}
