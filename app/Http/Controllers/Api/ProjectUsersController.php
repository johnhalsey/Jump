<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use App\Events\UserAddedToProject;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\StoreProjectUserRequest;
use App\Http\Requests\DestroyProjectUserRequest;

class ProjectUsersController extends Controller
{
    public function store(StoreProjectUserRequest $request, Project $project)
    {
        // create or get user by email
        $user = User::query()->where('email', $request->input('email'))->first();

        if (!$user) {
            $user = $this->registerUser($request->input('email'));
        }

        // invite user
        $project->users()->attach($user->id);
        event(new UserAddedToProject($user, $project));

        return response()->json();
    }

    public function destroy(DestroyProjectUserRequest $request, Project $project, User $user): Response
    {
        $project->users()->detach($user->id);

        return response(null, 204);
    }

    private function inviteUser($email)
    {
        // create invitation and email them
    }
}
