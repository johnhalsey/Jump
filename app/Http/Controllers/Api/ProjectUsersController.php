<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectUserRequest;
use App\Http\Requests\DestroyProjectUserRequest;

class ProjectUsersController extends Controller
{
    public function store(Request $request, Project $project)
    {
        // invitation has been accepted, user has registered the user can be attched to the project now
        // fire new event
    }

    public function destroy(DestroyProjectUserRequest $request, Project $project, User $user): Response
    {
        $project->users()->detach($user->id);

        return response(null, 204);
    }
}
