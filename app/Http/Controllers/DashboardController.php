<?php

namespace App\Http\Controllers;

use App\Enums\DefaultProjectStatus;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // send the users projects along to the jsx
        $projects = $request->user()->projects()
            ->with('statuses', function ($query) {
                $query->withCount(['tasks']);
            })
            ->with('users')
            ->with('owners')
            ->get();

        return Inertia::render('Dashboard', [
                'projects'         => ProjectResource::collection($projects),
                'default_statuses' => DefaultProjectStatus::cases(),
            ]
        );
    }
}
