<?php

namespace App\Http\Controllers\Auth;

use App\Models\Project;
use App\Models\Invitation;
use App\Events\UserAddedToProject;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Session;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        $props = [];
        if ($request->has('project_id')) {
            $props['project_id'] = $request->input('project_id');
            $props['auto_accept'] = (bool)$request->input('auto_accept', false);
        }

        return Inertia::render('Auth/Register', $props);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'project_id' => 'integer|exists:projects,id',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);

        if ($request->has('project_id')) {
            $invitation = Invitation::query()
                ->where('project_id', $request->input('project_id'))
                ->where('email', $request->email)
                ->first();

            if ($invitation) {
                $project = Project::find($request->has('project_id'));
                $project->users()->attach($user->id);
                $invitation->delete();

                // fire event

                event(new UserAddedToProject($user, $project));
            }
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
