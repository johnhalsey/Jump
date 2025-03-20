<?php

namespace App\Http\Controllers\Api\Project;

use App\Models\Project;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Events\UserInvitedToProject;
use App\Http\Requests\StoreProjectInvitationRequest;

class InvitationController extends Controller
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
