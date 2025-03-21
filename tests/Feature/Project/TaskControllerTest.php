<?php

namespace Tests\Feature\Project;

use Tests\TestCase;
use App\Models\Project;
use App\Models\TaskNote;
use App\Models\ProjectTask;
use App\Models\ProjectStatus;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_auth_can_view_task()
    {
        $project = Project::factory()->create();
        $status = ProjectStatus::factory()->create([
            'project_id' => $project->id,
        ]);
        $projectTask = ProjectTask::factory()->create([
            'project_id' => $project->id,
            'status_id' => $status->id,
        ]);
        TaskNote::factory()->count(5)->create([
            'task_id' => $projectTask->id,
        ]);
        $projectTask->project->users()->attach($projectTask->assignee);
        $this->actingAs($projectTask->assignee);

        $this->get(route('project.task.show', ['project' => $project, 'projectTask' => $projectTask]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Project/Task/Show')
                ->has('task')
            );
    }
}
