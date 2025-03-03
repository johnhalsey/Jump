<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\TaskNote;
use App\Models\ProjectTask;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskNoteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_user_can_add_note_to_task()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses->first()->id,
        ]);
        $project->users()->attach($user->id);
        $this->actingAs($user);

        $this->assertCount(0, $task->notes);

        $response = $this->json(
            'POST',
            '/api/project/' . $project->id . '/task/' . $task->id . '/notes',
            [
                'note' => 'test note',
            ]
        )->assertStatus(201);

        $task = $task->refresh();
        $this->assertCount(1, $task->notes);
    }

    public function test_cannot_add_note_if_note_is_invalid()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses->first()->id,
        ]);
        $project->users()->attach($user->id);
        $this->actingAs($user);

        $this->assertCount(0, $task->notes);

        $response = $this->json(
            'POST',
            '/api/project/' . $project->id . '/task/' . $task->id . '/notes',
            [
                'note' => '',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('note');
    }

    public function test_non_project_user_cannot_add_note()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses->first()->id,
        ]);

        $this->actingAs($user);

        $this->assertCount(0, $task->notes);

        $response = $this->json(
            'POST',
            '/api/project/' . $project->id . '/task/' . $task->id . '/notes',
            [
                'note' => 'test note',
            ]
        )->assertStatus(403);
    }

    public function test_guest_project_user_cannot_add_note()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses->first()->id,
        ]);

        $project->users()->attach($user->id);
        $this->assertCount(0, $task->notes);

        $response = $this->json(
            'POST',
            '/api/project/' . $project->id . '/task/' . $task->id . '/notes',
            [
                'note' => 'test note',
            ]
        )->assertStatus(401);
    }

    public function test_project_user_can_update_note()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses->first()->id,
        ]);
        $note = TaskNote::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'note' => 'i am a test note',
        ]);
        $project->users()->attach($user->id);
        $this->actingAs($user);

        $response = $this->json(
            'PATCH',
            '/api/project/' . $project->id . '/task/' . $task->id . '/note/' . $note->id,
            [
                'note' => 'i am an UPDATED note',
            ]
        )->assertStatus(200);

        $note = $note->refresh();

        $this->assertEquals('i am an UPDATED note', $note->note);
    }

    public function test_non_project_cannot_update_note()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses->first()->id,
        ]);
        $note = TaskNote::factory()->create([
            'task_id' => $task->id,
            'note' => 'i am a test note',
        ]);
        $this->actingAs($user);

        $response = $this->json(
            'PATCH',
            '/api/project/' . $project->id . '/task/' . $task->id . '/note/' . $note->id,
            [
                'note' => 'i am an UPDATED note',
            ]
        )->assertStatus(403);

        $note = $note->refresh();

        $this->assertEquals('i am a test note', $note->note);
    }

    public function test_note_cannot_be_updated_if_request_data_invalid()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses->first()->id,
        ]);
        $note = TaskNote::factory()->create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'note' => 'i am a test note',
        ]);
        $project->users()->attach($user->id);
        $this->actingAs($user);

        $this->json(
            'PATCH',
            '/api/project/' . $project->id . '/task/' . $task->id . '/note/' . $note->id,
            [
                'note' => '',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('note')
            ->assertJsonFragment([
                'message' => 'The note field is required.',
            ]);

        $this->json(
            'PATCH',
            '/api/project/' . $project->id . '/task/' . $task->id . '/note/' . $note->id,
            [
                'note' => 123,
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('note')
            ->assertJsonFragment([
                'message' => 'The note field must be a string.',
            ]);
    }

}
