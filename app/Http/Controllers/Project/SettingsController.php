<?php

namespace App\Http\Controllers\Project;

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $project->load('invitations');

        return Inertia::render('Project/Settings');
    }
}
