<?php

namespace Tests\Feature\Api\Project;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Invitation;
use App\Events\UserInvitedToProject;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_fails_when_inviting_new_user_to_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($user, ['owner' => true]);
        $this->actingAs($user);

        $this->assertTrue($user->hasVerifiedEmail());

        $this->json(
            'POST',
            route('api.project.invitations.store', ['project' => $project]),
            [
                'email' => '',
            ]
        )
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJsonFragment([
                'errors' => [
                    'email' => ['The email field is required.']
                ]
            ]);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/invitations',
            [
                'email' => 123,
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJsonFragment([
                'errors' => [
                    'email' => [
                        'The email field must be a string.',
                        'The email field must be a valid email address.',
                    ]
                ]
            ]);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/invitations',
            [
                'email' => 'kjshdkaj',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJsonFragment([
                'errors' => [
                    'email' => [
                        'The email field must be a valid email address.',
                    ]
                ]
            ]);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/invitations',
            [
                'email' => '12345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890@example.com',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJsonFragment([
                'errors' => [
                    'email' => [
                        'The email field must not be greater than 255 characters.',
                    ]
                ]
            ]);

        Invitation::factory()->create([
            'project_id' => $project->id,
            'email' => 'johndoe@example.com',
        ]);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/invitations',
            [
                'email' => 'johndoe@example.com',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJsonFragment([
                'errors' => [
                    'email' => [
                        'This user has already been invited to this project.',
                    ]
                ]
            ]);

        $anotherUser = User::factory()->create();
        $project->users()->attach($anotherUser);

        $this->assertCount(2, $project->users);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/invitations',
            [
                'email' => $anotherUser->email,
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJsonFragment([
                'errors' => [
                    'email' => [
                        'This user has already been added to this project.',
                    ]
                ]
            ]);
    }

    public function test_project_owner_can_invite_user_to_project()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $project = Project::factory()->create();
        $project->users()->attach($user, ['owner' => true]);
        $this->assertCount(1, $project->users);
        $this->actingAs($user);

        $this->assertDatabaseMissing('invitations', [
            'email' => 'test2@example.com',
        ]);

        Event::fake();
        $this->json(
            'POST',
            '/api/project/' . $project->id . '/invitations',
            [
                'email' => 'test2@example.com',
            ]
        )->assertStatus(200);

        $this->assertDatabaseHas('invitations', [
            'email' => 'test2@example.com',
        ]);

        Event::assertDispatched(UserInvitedToProject::class);
    }

    public function test_project_user_cannot_invite_user_to_project()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $project = Project::factory()->create();
        $project->users()->attach($user);
        $this->assertCount(1, $project->users);
        $this->actingAs($user);

        $this->assertDatabaseMissing('invitations', [
            'email' => 'test2@example.com',
        ]);

        Event::fake();

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/invitations',
            [
                'email' => 'test2@example.com',
            ]
        )->assertStatus(403);

        $this->assertDatabaseMissing('invitations', [
            'email' => 'test2@example.com',
        ]);

        Event::assertNotDispatched(UserInvitedToProject::class);
    }
}
