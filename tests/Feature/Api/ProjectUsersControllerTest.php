<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Events\UserAddedToProject;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectUsersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_owner_can_remove_project_user()
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($owner, ['owner' => true]);

        $user = User::factory()->create();
        $project->users()->attach($user);

        $this->assertCount(2, $project->users);

        $this->actingAs($owner);
        $this->json(
            'DELETE',
            '/api/project/' . $project->id . '/user/' . $user->id
        )->assertStatus(204);

        $project->refresh();
        $this->assertCount(1, $project->users);
    }

    public function test_project_user_cannot_remove_user()
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($owner, ['owner' => true]);

        $user = User::factory()->create();
        $project->users()->attach($user);

        $this->assertCount(2, $project->users);

        $this->actingAs($user);
        $this->json(
            'DELETE',
            '/api/project/' . $project->id . '/user/' . $user->id
        )->assertStatus(403);

        $project->refresh();
        $this->assertCount(2, $project->users);
    }

    public function test_last_project_owner_cannot_be_removed()
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($owner, ['owner' => true]);

        $this->assertCount(1, $project->users);

        $this->actingAs($owner);
        $this->json(
            'DELETE',
            '/api/project/' . $project->id . '/user/' . $owner->id
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('user')
            ->assertJsonFragment([
                'message' => $owner->name . ' cannot be removed because they are the only owner of this project.',
            ]);

        $project->refresh();
        $this->assertCount(1, $project->users);
    }

    public function test_validation_fails_when_adding_new_user_to_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($user, ['owner' => true]);
        $this->actingAs($user);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/users',
            [
                'email' => '',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrorFor('email')
            ->assertJsonFragment([
                'errors' => [
                    'email' => ['The email field is required.']
                ]
            ]);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/users',
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
            '/api/project/' . $project->id . '/users',
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
            '/api/project/' . $project->id . '/users',
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

        $anotherUser = User::factory()->create();
        $project->users()->attach($anotherUser);

        $this->assertCount(2, $project->users);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/users',
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

    public function test_project_owner_can_add_new_user_to_project()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $project = Project::factory()->create();
        $project->users()->attach($user, ['owner' => true]);
        $this->assertCount(1, $project->users);
        $this->actingAs($user);

        $this->assertDatabaseMissing('users', [
            'email' => 'test2@example.com',
        ]);

        Event::fake();
        $this->json(
            'POST',
            '/api/project/' . $project->id . '/users',
            [
                'email' => 'test2@example.com',
            ]
        )->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => 'test2@example.com',
        ]);
        $project->refresh();
        $this->assertCount(2, $project->users);
        Event::assertDispatched(Registered::class);
        Event::assertDispatched(UserAddedToProject::class);
    }

    public function test_project_owner_can_add_existing_db_user_to_project()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $project = Project::factory()->create();
        $project->users()->attach($user, ['owner' => true]);
        $this->assertCount(1, $project->users);
        $this->actingAs($user);
        $user2 = User::factory()->create([
            'email' => 'test2@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test2@example.com',
        ]);

        Event::fake();
        $this->json(
            'POST',
            '/api/project/' . $project->id . '/users',
            [
                'email' => 'test2@example.com',
            ]
        )->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => 'test2@example.com',
        ]);
        $project->refresh();
        $this->assertCount(2, $project->users);
        Event::assertNotDispatched(Registered::class);
        Event::assertDispatched(UserAddedToProject::class);
    }

    public function test_project_user_cannot_add_new_user_to_project()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $project = Project::factory()->create();
        $project->users()->attach($user);
        $this->assertCount(1, $project->users);
        $this->actingAs($user);

        $this->assertDatabaseMissing('users', [
            'email' => 'test2@example.com',
        ]);

        $this->json(
            'POST',
            '/api/project/' . $project->id . '/users',
            [
                'email' => 'test2@example.com',
            ]
        )->assertStatus(403);

        $project->refresh();
        $this->assertCount(1, $project->users);

        $this->assertDatabaseMissing('users', [
            'email' => 'test2@example.com',
        ]);
    }
}
