<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectInvitationController extends Controller
{
    public function accept(Request $request, Project $project, string $email, string $token)
    {
        dd($request->all(), $email, $token);

        // find invitation
        // find user if it exists, or create it
        // attach user to project
        // redirect to dashboard
    }
}
