<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Invitation;
use Illuminate\Http\Request;
use App\Events\UserAddedToProject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ProjectInvitationController extends Controller
{
    public function accept(Request $request, Project $project, string $email, string $token)
    {
        $invitation = Invitation::query()
            ->where('project_id', $project->id)
            ->where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$invitation) {
            Log::error("Invitation with this email '{$email}' not found");
            // supermassive black hole?
            return redirect()->route('dashboard');
        }

        // if the user does not already exist, send them to the registration form, with some additional params
        // which we can catch on the registration controller, after submition so we can auto add them
        // to the project once registered

        if (!$user = User::query()->where('email', $email)->first()) {
            // log out existing user, in case there is one logged in.
            Auth::logout();
            return redirect()->route('register', [
                'project_id'  => $project->id,
                'auto_accept' => true
            ]);
        }

        // The user does exist already, we can attach them to the project, and redirect them to the dashboard

        if (Auth::check() && Auth::id() !== $user->id) {
            // log the current user out if it is not the one in the invitation
            Auth::logout();
        }

        $project->users()->attach($user);
        $invitation->delete();

        event(new UserAddedToProject($user, $project));

        return redirect()->route('dashboard');
    }
}
