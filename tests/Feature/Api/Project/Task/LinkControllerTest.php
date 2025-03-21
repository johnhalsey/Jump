<?php

namespace Tests\Feature\Api\Project\Task;

use Tests\TestCase;
use App\Models\Link;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectTask;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LinkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_user_can_index_task_links()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);
        $links = Link::factory()->count(5)->make([
            'linkable_id' => $task->id,
        ]);

        $task->links()->saveMany($links);

        $user = User::factory()->create();
        $project->users()->attach($user);
        $this->actingAs($user);

        $resonse = $this->json(
            'GET',
            route('api.project.task.links.index', [
                'project' => $project,
                'projectTask' => $task,
            ])
        )
            ->assertStatus(200);

        $data = json_decode($resonse->getContent(), true)['data'];
        $this->assertCount(5, $data);
    }

    public function test_guest_user_cannot_index_project_task_links()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);
        $links = Link::factory()->count(5)->make([
            'linkable_id' => $task->id,
        ]);

        $task->links()->saveMany($links);

        $resonse = $this->json(
            'GET',
            route('api.project.task.links.index', [
                'project' => $project,
                'projectTask' => $task,
            ])
        )
            ->assertStatus(401);
    }

    public function test_non_project_user_cannot_index_project_task_links()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);
        $links = Link::factory()->count(5)->make([
            'linkable_id' => $task->id,
        ]);

        $task->links()->saveMany($links);

        $user = User::factory()->create();
        $this->actingAs($user);

        $resonse = $this->json(
            'GET',
            route('api.project.task.links.index', [
                'project' => $project,
                'projectTask' => $task,
            ])
        )
            ->assertStatus(403);
    }

    public function test_project_user_can_save_task_link()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);

        $user = User::factory()->create();
        $project->users()->attach($user);
        $this->actingAs($user);

        $this->assertCount(0, $task->links);

        $this->json(
            'POST',
            route('api.project.task.links.store', [
                'project' => $project,
                'projectTask' => $task,
            ]),
            [
                'text' => 'My Test Link',
                'url' => 'http://my-test-link',
            ]
        )->assertStatus(201);

        $task = $task->refresh();
        $this->assertCount(1, $task->links);
    }

    public function test_store_task_link_validation_will_fail_if_text_invalid()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);

        $user = User::factory()->create();
        $project->users()->attach($user);
        $this->actingAs($user);

        $this->json(
            'POST',
            route('api.project.task.links.store', [
                'project' => $project,
                'projectTask' => $task,
            ]),
            [
                'text' => 111,
                'url' => 'http://my-test-link',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors(['text']);
    }

    public function test_store_task_link_validation_will_fail_if_url_invalid()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);

        $user = User::factory()->create();
        $project->users()->attach($user);
        $this->actingAs($user);

        $this->json(
            'POST',
            route('api.project.task.links.store', [
                'project' => $project,
                'projectTask' => $task,
            ]),
            [
                'text' => 'My Test Link',
                'url' => 'my-test-link',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors(['url']);

        $this->json(
            'POST',
            route('api.project.task.links.store', [
                'project' => $project,
                'projectTask' => $task,
            ]),
            [
                'text' => 'My Test Link',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors(['url']);

        $this->json(
            'POST',
            route('api.project.task.links.store', [
                'project' => $project,
                'projectTask' => $task,
            ]),
            [
                'text' => 'My Test Link',
                'url' => '',
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors(['url']);

        $this->json(
            'POST',
            route('api.project.task.links.store', [
                'project' => $project,
                'projectTask' => $task,
            ]),
            [
                'text' => 'My Test Link',
                'url' => 111,
            ]
        )->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    public function test_project_user_can_update_link()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);
        $link = Link::factory()->make([
            'linkable_id' => $task->id,
            'text' => 'I am a Link',
            'url' => 'http://my-test-link',
        ]);
        $task->links()->save($link);

        $user = User::factory()->create();
        $project->users()->attach($user);
        $this->actingAs($user);

        $updatedName = 'I am an updated Link';
        $updatedUrl = 'http://my-test-link';

        $resonse = $this->json(
            'PATCH',
            route('api.project.task.links.update', [
                'project' => $project,
                'projectTask' => $task,
                'link' => $link,
            ]),
            [
                'text' => $updatedName,
                'url' => $updatedUrl,
            ]
        )
            ->assertStatus(200);

        $data = json_decode($resonse->getContent(), true)['data'];
        $this->assertEquals($updatedName, $data['text']);
        $this->assertEquals($updatedUrl, $data['url']);

        $link = Link::first();

        $this->assertEquals($updatedName, $link->text);
        $this->assertEquals($updatedUrl, $link->url);
    }

    public function test_project_user_can_delete_task_link()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $project->statuses()->first()->id,
        ]);
        $link = Link::factory()->make([
            'linkable_id' => $task->id,
        ]);
        $task->links()->save($link);

        $user = User::factory()->create();
        $project->users()->attach($user);
        $this->actingAs($user);

        $this->assertCount(1, Link::all());

        $resonse = $this->json(
            'DELETE',
            route('api.project.task.links.update', [
                'project' => $project,
                'projectTask' => $task,
                'link' => $link,
            ]),
        )
            ->assertStatus(204);

        $this->assertCount(0, Link::all());
    }
}
