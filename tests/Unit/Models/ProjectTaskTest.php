<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Link;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectStatus;
use App\Enums\DefaultProjectStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_observer_will_add_reference_when_created()
    {
        $project = Project::factory()->create();
        $status = ProjectStatus::factory()->create([
            'project_id' => $project->id,
            'name'       => DefaultProjectStatus::TO_DO->value
        ]);
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id'  => $status->id,
        ]);

        $this->assertNotNull($task->reference);

        $task = $project->tasks()->create([
            'title'       => 'New Task',
            'description' => 'I am a paragraph',
            'project_id'  => Project::factory()->create()->id,
            'assignee_id' => User::factory()->create()->id,
            'creator_id'  => User::factory()->create()->id,
            'status_id'   => $status->id,
        ]);

        $this->assertNotNull($task->reference);
    }

    public function test_task_links()
    {
        $project = Project::factory()->create();
        $task = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id'  => $project->statuses()->first()->id,
        ]);
        $link = Link::factory()->make([
            'linkable_id' => $task->id,
        ]);

        $task->links()->save($link);

        $this->assertEquals($task->links()->count(), 1);
        $this->assertDatabaseHas('links', [
            'linkable_id' => $task->id,
            'linkable_type' => ProjectTask::class,
            'name' => $link->name,
            'url' => $link->url,
        ]);
    }
}
