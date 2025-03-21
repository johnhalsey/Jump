<?php

namespace Tests\Feature\Project;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Invitation;
use App\Events\UserAddedToProject;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_404_if_project_not_found()
    {
        $this->call(
            'GET',
            route('project.invitations.accept', [
                'project' => 1,
                'email' => 'john@example.com',
                'token' => 123456
            ])
        )->assertStatus(404)
            ->assertSee('Not Found');
    }

    public function test_403_if_route_not_signed()
    {
        $project = Project::factory()->create();

        $this->call(
            'GET',
            route('project.invitations.accept', [
                'project' => $project->id,
                'email' => 'john@example.com',
                'token' => 123456
            ])
        )->assertStatus(403)
            ->assertSee('Invalid signature.');
    }

    public function test_user_rediredted_to_dashboard_invitiation_doesnt_exist()
    {
        $project = Project::factory()->create();

        $signedUrl = URL::temporarySignedRoute(
            'project.invitations.accept', now()->addHours(2), [
                'project' => $project->id,
                'email'   => 'john@example.com',
                'token'   => 'ABCDEFGHIJKLMNOPQRSTUV',
            ]
        );

        $this->call(
            'GET',
            $signedUrl,
        )->assertStatus(302)
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('invitations', 0);
    }

    public function test_redirected_to_registration_page_if_invited_user_not_exists()
    {
        $project = Project::factory()->create();

        $invite = Invitation::factory()->create([
            'project_id' => $project->id,
            'email'      => 'john@example.com',
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'project.invitations.accept', now()->addHours(2), [
                'project' => $project->id,
                'email'   => 'john@example.com',
                'token'   => $invite->token,
            ]
        );

        $this->call(
            'GET',
            $signedUrl,
        )->assertStatus(302)
            ->assertRedirect(route('register', [
                'project_id'  => $project->id,
                'auto_accept' => true
            ]));
    }

    public function test_existing_user_will_be_attached_to_project()
    {
        $project = Project::factory()->create();

        $user = User::factory()->create();

        $invite = Invitation::factory()->create([
            'project_id' => $project->id,
            'email'      => $user->email,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'project.invitations.accept', now()->addHours(2), [
                'project' => $project->id,
                'email'   => $user->email,
                'token'   => $invite->token,
            ]
        );

        $this->assertCount(0, $user->projects);
        $this->assertCount(1, Invitation::all());

        Event::fake();

        $this->call(
            'GET',
            $signedUrl,
        )->assertStatus(302)
            ->assertRedirect(route('dashboard'));

        $user = $user->refresh();
        $this->assertCount(1, $user->projects);
        $this->assertCount(0, Invitation::all());
        Event::assertDispatched(UserAddedToProject::class);
    }

    public function test_it_will_log_out_different_user()
    {
        $project = Project::factory()->create();

        $user = User::factory()->create();
        // user 2 will be logged in
        $user2 = User::factory()->create();
        $this->actingAs($user2);

        $invite = Invitation::factory()->create([
            'project_id' => $project->id,
            'email'      => $user->email,
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'project.invitations.accept', now()->addHours(2), [
                'project' => $project->id,
                'email'   => $user->email,
                'token'   => $invite->token,
            ]
        );

        $this->assertAuthenticatedAs($user2);

        $this->call(
            'GET',
            $signedUrl,
        )->assertStatus(302)
            ->assertRedirect(route('dashboard'));

        $this->assertGuest();
    }
}
