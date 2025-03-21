<?php

namespace App\Http\Controllers\Api\Project;

use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\DefaultProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectTaskResource;
use App\Http\Requests\StoreProjectTaskRequest;
use App\Http\Requests\UpdateProjectTaskRequest;
use App\Http\Requests\IndexProjectTasksRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskController extends Controller
{
    public function index(IndexProjectTasksRequest $request, Project $project): JsonResource
    {
        $query = $project->tasks()->with(['project', 'assignee', 'status']);

        if ($request->has('search') && !empty($request->get('search'))) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('reference', 'like', '%' . $request->get('search') . '%');
            });
        }

        if ($request->has('userIds')) {
            $query->where(function ($query) use ($request) {
                $query->whereIn('assignee_id', $request->get('userIds'));
                if (in_array(NULL, $request->get('userIds'))) {
                    $query->orWhereNull('assignee_id');
                }
            });

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
        $projectTask->update($request->validated());

        return new ProjectTaskResource($projectTask->load(['project', 'assignee', 'status']));
    }

    public function destroy(Request $request, Project $project, ProjectTask $projectTask): Response
    {
        $projectTask->delete();

        return response()->noContent();
    }
}
