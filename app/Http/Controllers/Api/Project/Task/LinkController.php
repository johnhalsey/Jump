<?php

namespace App\Http\Controllers\Api\Project\Task;

use App\Models\Link;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\LinkResource;
use App\Http\Requests\StoreLinkRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class LinkController extends Controller
{
    public function index(Request $request, Project $project, ProjectTask $projectTask): JsonResource
    {
        return LinkResource::collection($projectTask->links);
    }

    public function store(StoreLinkRequest $request, Project $project, ProjectTask $projectTask): JsonResource
    {
        $link = $projectTask->links()->create($request->validated());

        return new LinkResource($link);
    }

    public function update(StoreLinkRequest $request, Project $project, ProjectTask $projectTask, Link $link): JsonResource
    {
        $link->update($request->validated());

        return new LinkResource($link);
    }

    public function destroy(Project $project, ProjectTask $projectTask, Link $link): JsonResponse
    {
        $link->delete();

        return response()->json(status: HttpResponse::HTTP_NO_CONTENT);
    }
}
