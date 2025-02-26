<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Http\Resources\TaskNoteResource;
use Illuminate\Console\View\Components\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

}
