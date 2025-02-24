<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
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
}
