<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_owner_can_update_project_settings()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'Test Project',
        ]);
        $shortCode = $project->short_code;
        $project->users()->attach($user->id, ['owner' => true]);
        $this->actingAs($user);

        $response = $this->json(
            'PATCH',
            route('api.project.settings.update',['project' => $project]),
            [
                'name' => 'John\'s Project',
                'short_code' => 'ABCD'
            ])->assertStatus(200);

        $project->refresh();
        $this->assertSame('John\'s Project', $project->name);
        $this->assertSame('ABCD', $project->short_code);
        $this->assertNotSame($shortCode, $project->short_code);
    }

    public function test_project_user_cannot_update_project_settings()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'Test Project',
        ]);
        $shortCode = $project->short_code;
        $project->users()->attach($user->id, ['owner' => false]);
        $this->actingAs($user);

        $response = $this->json(
            'PATCH',
            route('api.project.settings.update',['project' => $project]),
            [
                'name' => 'John\'s Project',
                'short_code' => 'ABCD'
            ])->assertStatus(403);

        $project->refresh();
        $this->assertSame('Test Project', $project->name);
        $this->assertSame($shortCode, $project->short_code);
    }

    public function test_non_project_owner_user_cannot_update_project_settings()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'Test Project',
        ]);
        $shortCode = $project->short_code;

        $this->actingAs($user);

        $response = $this->json(
            'PATCH',
            route('api.project.settings.update',['project' => $project]),
            [
                'name' => 'John\'s Project',
                'short_code' => 'ABCD'
            ])->assertStatus(403);
    }

    public function test_validation_error_if_request_data_invalid()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create([
            'name' => 'Test Project',
        ]);
        $project->users()->attach($user->id, ['owner' => true]);
        $this->actingAs($user);

        $this->json(
            'PATCH',
            route('api.project.settings.update',['project' => $project]),
            [
                'name' => '',
                'short_code' => ''
            ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'short_code'])
            ->assertJsonFragment([
                'errors' => [
                    'name' => ['The name field is required.'],
                    'short_code' => ['The short code field is required.'],
                ]
            ]);

        $this->json(
            'PATCH',
            route('api.project.settings.update',['project' => $project]),
            [
                'name' => 'ABC',
                'short_code' => 'ABC'
            ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'short_code'])
            ->assertJsonFragment([
                'errors' => [
                    'name' => ['The name field must be at least 4 characters.'],
                    'short_code' => ['The short code field must be at least 4 characters.'],
                ]
            ]);

        $this->json(
            'PATCH',
            route('api.project.settings.update',['project' => $project]),
            [
                'name' => 123,
                'short_code' => 'ABC1234'
            ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'short_code'])
            ->assertJsonFragment([
                'errors' => [
                    'name' => ['The name field must be a string.', 'The name field must be at least 4 characters.'],
                    'short_code' => ['The short code field must not be greater than 6 characters.'],
                ]
            ]);

        $this->json(
            'PATCH',
            route('api.project.settings.update',['project' => $project]),
            [
                'name' => '1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456',
                'short_code' => 123
            ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'short_code'])
            ->assertJsonFragment([
                'errors' => [
                    'name' => ['The name field must not be greater than 255 characters.'],
                    'short_code' => ['The short code field must be a string.', 'The short code field must be at least 4 characters.'],
                ]
            ]);

    }
}
