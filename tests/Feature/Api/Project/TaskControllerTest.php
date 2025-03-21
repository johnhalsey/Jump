<?php

namespace Tests\Feature\Api\Project;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\TaskNote;
use App\Models\ProjectTask;
use App\Models\ProjectStatus;
use App\Enums\DefaultProjectStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
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
        $this->assertFalse(isset($data[0]['notes']));  // notes not included in the resource unless loaded
    }

    public function test_can_filter_tasks_by_search_term()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $status = ProjectStatus::factory()->create([
            'project_id' => $project->id,
            'name'       => DefaultProjectStatus::TO_DO->value
        ]);
        $task1 = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id'  => $status->id,
            'title'      => 'Task Title'
        ]);

        $task2 = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id'  => $status->id,
            'title'      => 'I am not a description'
        ]);

        $task3 = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id'  => $status->id,
            'title'      => 'Im good ta'
        ]);

        $user->projects()->attach($project->id);
        $this->actingAs($user);

        $response = $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'search' => 'ta'
            ]
        )->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertSame($task1->id, $data[0]['id']);
        $this->assertSame($task3->id, $data[1]['id']);

        $response = $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'search' => $task2->reference
            ]
        )->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertSame($task2->id, $data[0]['id']);
    }

    public function test_request_invalid_if_search_not_string()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user->id]);

        $this->actingAs($user);
        $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'search' => ['search text'],
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('search');
    }

    public function test_can_filter_tasks_by_assignee()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();
        $user5 = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user->id, $user2->id, $user3->id, $user4->id, $user5->id]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user2->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user3->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user4->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user5->id,
        ]);

        $this->actingAs($user);
        $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'userIds' => [$user->id],
            ]
        )->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $this->actingAs($user);
        $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'userIds' => [$user3->id, $user2->id],
            ]
        )->assertStatus(200)
            ->assertJsonCount(4, 'data');

    }

    public function test_can_filter_by_unassigned_assignee()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();
        $user5 = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user->id, $user2->id, $user3->id, $user4->id, $user5->id]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user2->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user3->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user4->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user5->id,
        ]);

        ProjectTask::factory()->count(2)->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => NULL
        ]);


        $this->actingAs($user);
        $response = $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'userIds' => [NULL],
            ]
        )->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $json = $response->json()['data'];
        foreach ($json as $task) {
            $this->assertNull($task['assignee']);
        }

        $this->actingAs($user);
        $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'userIds' => [$user3->id, $user2->id, NULL],
            ]
        )->assertStatus(200)
            ->assertJsonCount(6, 'data');
    }

    public function test_request_invalid_if_userids_not_array()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user->id]);

        $this->actingAs($user);
        $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'userIds' => $user->id,
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors('userIds');
    }

    public function test_can_filter_tasks_by_userIds_and_search()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();
        $user4 = User::factory()->create();
        $user5 = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user->id, $user2->id, $user3->id, $user4->id, $user5->id]);

        ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user->id,
            'title'       => 'ABC'
        ]);

        ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user2->id,
            'title'       => 'ABC'
        ]);

        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user3->id,
        ]);
        $task->update([
            'reference' => 'ABC'
        ]);

        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user4->id,
        ]);
        $task->update([
            'reference' => 'ABC'
        ]);

        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses->first()->id,
            'assignee_id' => $user5->id,
            'title'       => 'No Match',
        ]);
        $task->update([
            'reference'   => 'ZZ-XX'
        ]);

        $this->actingAs($user);
        $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'search' => 'BC',
                'userIds' => [$user->id],
            ]
        )->assertStatus(200)
            ->assertJsonCount(1, 'data');

        $this->actingAs($user);
        $this->json(
            'GET',
            'api/project/' . $project->id . '/tasks',
            [
                'search' => 'BC',
                'userIds' => [$user2->id, $user3->id, $user5->id],
            ]
        )->assertStatus(200)
            ->assertJsonCount(2, 'data');
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
            '/api/project/' . $project->id . '/tasks'
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
            'title'       => 'Task Title'
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
                'description' => 'I am a description',
                'title'       => 'Updated Task Title'
            ]
        )->assertStatus(200);

        $task = $task->refresh();
        $this->assertSame($user2->id, $task->assignee_id);
        $this->assertSame($inProgressStatus->id, $task->status_id);
        $this->assertSame('I am a description', $task->description);
        $this->assertSame('Updated Task Title', $task->title);

    }

    public function test_task_assignee_can_set_to_null()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $project->statuses()->where('name', DefaultProjectStatus::TO_DO->value)->first()->id,
            'assignee_id' => $user->id,
            'title'       => 'Task Title'
        ]);

        $this->assertNotNull($task->assignee_id);

        $project->users()->attach($user);
        $this->actingAs($user);

        $this->json(
            'PATCH',
            'api/project/' . $project->id . '/task/' . $project->tasks->first()->id,
            [
                'assignee_id' => null,
            ]
        )->assertStatus(200);

        $task = $task->refresh();
        $this->assertNull($task->assignee_id);
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
                'status_id' => $newStatus->id,
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
            ->assertJsonValidationErrors('title')
            ->assertJsonFragment([
                'message' => 'A task title is required.',
            ]);
    }

    public function test_project_user_can_delete_task()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($user);
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id'  => $project->statuses()->first()->id,
        ]);

        $this->assertCount(1, ProjectTask::all());
        $this->actingAs($user);
        $response = $this->json(
            'DELETE',
            'api/project/' . $project->id . '/task/' . $task->id,
        )->assertStatus(204);
        $this->assertCount(0, ProjectTask::all());
    }

    public function test_project_user_cannot_delete_task_for_different_project()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->attach($user);
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id'  => $project->statuses()->first()->id,
        ]);

        $project2 = Project::factory()->create();
        $task2 = ProjectTask::factory()->create([
            'project_id' => $project2->id,
            'status_id'  => $project2->statuses()->first()->id,
        ]);

        $this->actingAs($user);
        $this->json(
            'DELETE',
            'api/project/' . $project->id . '/task/' . $task2->id,
        )->assertStatus(403);

    }

    public function test_updating_task_assignee_will_not_overwrite_description_or_status()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()->create();
        $project->users()->sync([$user, $user2]);

        $inprogressStatus = $project->statuses()->where('name', 'In Progress')->first();
        $task = ProjectTask::factory()->create([
            'project_id'  => $project->id,
            'status_id'   => $inprogressStatus->id,
            'description' => 'I am a descrtiption',
            'assignee_id' => $user->id,
        ]);

        $this->actingAs($user);
        $response = $this->json(
            'PATCH',
            'api/project/' . $project->id . '/task/' . $task->id,
            [
                'assignee_id' => $user2->id,
            ]
        )->assertStatus(200);

        $task = $task->fresh();
        $this->assertEquals($task->assignee_id, $user2->id);
        $this->assertEquals('I am a descrtiption', $task->description);
        $this->assertEquals($inprogressStatus->id, $task->status_id);
    }
}
