<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_user_can_access_project_settings_page()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($user);
        $this->actingAs($user);

        $this->get(route('project.settings.index', ['project' => $project]))
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Project/Settings')
            );
    }

    public function test_non_project_user_cannot_access_project_settings_page()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        // do not attach use to project
        $this->actingAs($user);

        $this->get(route('project.settings.index', ['project' => $project]))
            ->assertStatus(302)
            ->assertRedirect(route('dashboard'));
    }
}
