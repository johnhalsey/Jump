<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\UpdateProjectSettingsRequest;

class ProjectSettingsController extends Controller
{
    public function update(UpdateprojectSettingsRequest $request, Project $project): JsonResource
    {
        $project->update([
            'name' => $request->input('name'),
            'short_code' => $request->input('short_code'),
        ]);

        return new ProjectResource($project);
    }
}
