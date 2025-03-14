<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectSettingsController extends Controller
{
    public function index(Request $request, Project $project)
    {
        return Inertia::render('Project/Settings');
    }
}
