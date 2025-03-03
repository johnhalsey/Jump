<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_user_can_create_project()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('api.projects.store'), [
            'name' => 'Test Project',
        ])->assertStatus(201);

        $this->assertDatabaseCount('projects', 1);
    }

    public function test_guest_cannot_create_project()
    {
        $this->postJson(route('api.projects.store'), [
            'name' => 'Test Project',
        ])->assertStatus(401);

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_validation_will_fail_if_name_invalid()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->postJson(route('api.projects.store'), [
            'name' => '',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('name')
            ->assertJsonFragment([
                'message' => 'The name field is required.',
            ]);

        $this->postJson(route('api.projects.store'), [
            'name' => false,
        ])->assertStatus(422)
            ->assertJsonValidationErrors('name')
            ->assertJsonFragment([
                'errors' => [
                    'name' => [
                        'The name field must be a string.',
                        'The name field must be at least 4 characters.'
                    ]
                ]
            ]);

        $this->postJson(route('api.projects.store'), [
            'name' => 'sh',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('name')
            ->assertJsonFragment([
                'message' => 'The name field must be at least 4 characters.',
            ]);

        $this->postJson(route('api.projects.store'), [
            'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
        ])->assertStatus(422)
            ->assertJsonValidationErrors('name')
            ->assertJsonFragment([
                'message' => 'The name field must not be greater than 255 characters.',
            ]);
    }

    public function test_user_will_be_attached_to_project_as_owner()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->assertCount(0, $user->projects);

        $response = $this->postJson(route('api.projects.store'), [
            'name' => 'Test Project',
        ])->assertStatus(201);

        $user->refresh();
        $this->assertCount(1, $user->projects);
        $data = $response->decodeResponseJson()['data'];
        $this->assertEquals($user->projects->first()->id, $data['id']);
        $project = Project::find($data['id']);
        $this->assertTrue($project->owners->contains($user));
    }
}
