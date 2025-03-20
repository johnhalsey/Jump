<?php

namespace App\Http\Controllers\Api\Project;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Request;
use App\Http\Requests\StoreProjectUserRequest;
use App\Http\Requests\DestroyProjectUserRequest;

class UserController extends Controller
{
    public function destroy(DestroyProjectUserRequest $request, Project $project, User $user): Response
    {
        $project->users()->detach($user->id);

        return response(null, 204);
    }
}
