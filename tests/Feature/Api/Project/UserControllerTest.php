<?php

namespace Tests\Feature\Api\Project;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
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
}
