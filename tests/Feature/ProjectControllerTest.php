<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_gate_will_stop_unauth_user()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        // not attaching user to project
        $this->actingAs($user);
        $this->call(
            'GET',
            '/project/' . $project->id,
        )->assertStatus(403);
    }

    public function test_gate_will_allow_user_to_access_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $user->projects()->attach($project);
        $this->actingAs($user);
        $this->call(
            'GET',
            '/project/' . $project->id,
        )->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('project', 1)
            );

    }
}
