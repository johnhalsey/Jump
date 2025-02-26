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
            'name'       => DefaultProjectStatus::TO_DO->value
        ]);
        ProjectTask::factory()->count(5)->create([
            'project_id' => $project->id,
            'status_id'  => $status->id,
        ])->each(function (ProjectTask $task) {
            TaskNote::factory()->count(5)->create([
                'task_id' => $task->id,
                'user_id' => $task->assignee_id,
            ]);
        });

        $user->projects()->attach($project->id);
        $this->actingAs($user);
        $response = $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks'
        )->assertStatus(200);

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertCount(5, $data); // 5 tasks
        $this->assertCount(5, $data[0]['notes']);
        // make sure all notes belong to the project
        for ($i = 0; $i < 5; $i++) {
            for ($n = 0; $n < 5; $n++) {
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
            'api/project/' . $project->id . '/tasks'
        )->assertStatus(403);
    }

    public function test_guest_is_unauthorized()
    {
        $project = Project::factory()->create();

        $response = $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks'
        )->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_project_user_can_update_project_task()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()->create();

        foreach (DefaultProjectStatus::cases() as $status) {
            ProjectStatus::create([
                'name'       => $status->value,
                'project_id' => $project->id,
            ]);
        }

        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses()->where('name', DefaultProjectStatus::TO_DO->value)->first()->id,
            'assignee_id' => null,
        ]);

        $this->assertNull($task->assignee_id);

        $project->users()->sync([$user, $user2]);
        $this->actingAs($user);

        $inProgressStatus = $project->statuses()->where('name', DefaultProjectStatus::IN_PROGRESS->value)->first();

        $response = $this->json(
            'PATCH',
            'api/project/' . $project->id . '/task/' . $project->tasks->first()->id,
            [
                'status_id'   => $inProgressStatus->id,
                'assignee_id' => $user2->id,
            ]
        )->assertStatus(200);

        $task = $task->refresh();
        $this->assertSame($user2->id, $task->assignee_id);
        $this->assertSame($inProgressStatus->id, $task->status_id);
    }

    public function test_cannot_update_task_with_assignee_who_is_not_in_project()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()->create();

        $status = ProjectStatus::create([
            'name'       => DefaultProjectStatus::TO_DO->value,
            'project_id' => $project->id,
        ]);

        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses()->where('name', DefaultProjectStatus::TO_DO->value)->first()->id,
            'assignee_id' => null,
        ]);

        $this->assertNull($task->assignee_id);

        // not attaching user2
        $project->users()->sync([$user]);
        $this->actingAs($user);

        $response = $this->json(
            'PATCH',
            'api/project/' . $project->id . '/task/' . $task->id,
            [
                'status_id'   => $status->id,
                'assignee_id' => $user2->id,
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('assignee_id');
    }

    public function test_cannot_update_task_with_status_that_does_not_belong_to_the_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $status = ProjectStatus::factory()->create([
            'project_id' => $project->id,
        ]);

        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'assignee_id' => null,
            'status_id'   => $status->id,
        ]);

        // not attaching user2
        $project->users()->sync([$user]);
        $this->actingAs($user);

        $newStatus = ProjectStatus::factory()->create();

        $response = $this->json(
            'PATCH',
            'api/project/' . $project->id . '/task/' . $task->id,
            [
                'status_id'   => $newStatus->id,
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('status_id');
    }

    public function test_project_user_can_store_new_task()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user]);

        $this->actingAs($user);
        $response = $this->json(
            'POST',
            'api/project/' . $project->id . '/tasks',
            [
                'title' => 'My new task title'
            ]
        )->assertStatus(201);
    }

    public function test_cannot_add_task_if_title_is_null()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user]);

        $this->actingAs($user);
        $response = $this->json(
            'POST',
            'api/project/' . $project->id . '/tasks',
            [
                'title' => null
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }


}
