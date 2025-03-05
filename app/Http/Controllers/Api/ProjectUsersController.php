<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyProjectUserRequest;

class ProjectUsersController extends Controller
{
    public function destroy(DestroyProjectUserRequest $request, Project $project, User $user): Response
    {
        $project->users()->detach($user->id);

        return response(null, 204);
    }
}
