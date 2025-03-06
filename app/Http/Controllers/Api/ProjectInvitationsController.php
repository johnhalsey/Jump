<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\UserInvitedToProject;
use App\Http\Requests\StoreProjectInvitationRequest;

class ProjectInvitationsController extends Controller
{
    public function store(StoreProjectInvitationRequest $request, Project $project): JsonResponse
    {
        $invitation = Invitation::create([
            'project_id' => $project->id,
            'email' => $request->input('email'),
        ]);

        event(new UserInvitedToProject($invitation));

        return response()->json();
    }
}
