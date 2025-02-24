<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\TaskNote;
use App\Models\ProjectTask;
use App\Models\ProjectStatus;
use App\Enums\DefaultProjectStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_user_can_index_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $status = ProjectStatus::factory()->create([
            'project_id' => $project->id,
            'name' => DefaultProjectStatus::TO_DO->value
        ]);
        ProjectTask::factory()->count(5)->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
        ]);

        $user->projects()->attach($project->id);
        $this->actingAs($user);
        $response = $this->json(
            'GET',
            'api/projects/' . $project->id . '/tasks'
        )->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertCount(5, $data); // 5 tasks
        $this->assertCount(5, $data[0]['notes']);
        // make sure all notes belong to the project
        for($i = 0; $i < 5; $i++){
            for($n = 0; $n < 5; $n++){
                $this->assertTrue(TaskNote::where('id', $data[$i]['notes'][$n]['id'])->whereHas('task.project', function ($query) use ($project) {
                    $query->where('project_id', $project->id);
                })->exists());
            }
        }
    }

    public function test_gate_will_stop_user_from_indexing_tasks()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $this->actingAs($user);
        $response = $this->json(
            'GET',
            'api/projects/' . $project->id . '/tasks'
        )->assertStatus(403);
    }

    public function test_guest_is_unauthorized()
    {
        $project = Project::factory()->create();

        $response = $this->json(
            'GET',
            'api/projects/' . $project->id . '/tasks'
        )->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.'
            ]);
    }
}
