<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TaskNoteResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskNoteController extends Controller
{
    public function store(Request $request, Project $project, ProjectTask $projectTask): JsonResource
    {
        $note = $projectTask->notes()->create([
            'note' => $request->input('note'),
            'user_id' => $request->user()->id,
        ]);

        return new TaskNoteResource($note);
    }
}
