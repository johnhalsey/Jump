<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\StoreProjectRequest;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectController extends Controller
{
    public function store(StoreProjectRequest $request): JsonResource
    {
        $project = Project::create([
            'name' => $request->get('name'),
        ]);

        $project->users()->attach($request->user(), ['owner' => true]);

        return new ProjectResource($project);
    }
}
