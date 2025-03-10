<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Auth\Access\Gate;
use App\Http\Resources\UserResource;
use App\Http\Resources\ProjectResource;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shareData = [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? new UserResource($request->user()) : null,
            ],
        ];

        if ($request->route('project')) {
            $shareData['project'] = new ProjectResource($request->route('project'));
        }

        return $shareData;
    }
}
