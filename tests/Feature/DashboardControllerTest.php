<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_via_dashboard()
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_auth_users_are_redirected_to_dashboard()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->get('/')->assertRedirect('/dashboard');
    }

    public function test_it_will_send_users_projects_to_dashboard()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($user);

        $this->actingAs($user);
        $this->call('GET', 'dashboard')
            ->assertStatus(200)
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->has('projects', 1)
            );
    }
}
