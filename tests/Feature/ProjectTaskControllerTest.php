<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectStatus;
use Database\Factories\ProjectFactory;
use Inertia\Testing\AssertableInertia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTaskControllerTest extends TestCase
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
