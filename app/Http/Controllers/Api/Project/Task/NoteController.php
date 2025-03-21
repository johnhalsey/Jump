<?php

namespace App\Http\Controllers\Api\Project\Task;

use App\Models\Project;
use App\Models\TaskNote;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskNoteResource;
use App\Http\Requests\StoreTaskNoteRequest;
use App\Http\Requests\UpdateTaskNoteRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class NoteController extends Controller
{
    public function index(Request $request, Project $project, ProjectTask $projectTask): JsonResource
    {
        return TaskNoteResource::collection($projectTask->notes()->paginate());
    }

    public function store(StoreTaskNoteRequest $request, Project $project, ProjectTask $projectTask): JsonResource
    {
        $note = $projectTask->notes()->create([
            'note'    => $request->input('note'),
            'user_id' => $request->user()->id,
        ]);

        return new TaskNoteResource($note);
    }

    public function update(UpdateTaskNoteRequest $request, Project $project, ProjectTask $projectTask, TaskNote $taskNote): JsonResource
    {
        $taskNote->update([
            'note' => $request->input('note'),
        ]);

        return new TaskNoteResource($taskNote);
    }

    public function destroy(Request $request, Project $project, ProjectTask $projectTask, TaskNote $taskNote): JsonResponse
    {
        $taskNote->delete();

        return response()->json(status: HttpResponse::HTTP_NO_CONTENT);
    }
}
