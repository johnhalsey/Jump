<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // send the users projects along to the jsx
        $projects = $request->user()->projects;

        return Inertia::render('Dashboard', [
            'projects' => ProjectResource::collection($projects),
        ]);

    }
}
