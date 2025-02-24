<?php

namespace Database\Seeders;

use App\Models\ProjectTask;
use App\Enums\DefaultProjectStatus;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Project::query()->delete();
        ProjectStatus::query()->delete();

        for($i = 0; $i < 10; $i++) {
            $project = Project::factory()->create();

            foreach(DefaultProjectStatus::cases() as $status) {
                ProjectStatus::create([
                    'name' => $status->value,
                    'project_id' => $project->id,
                ]);
            }

            ProjectTask::factory()->count(5)->create([
                'project_id' => $project->id,
                'status_id' => $project->statuses()->where('name', 'To Do')->first()->id,
            ]);

            ProjectTask::factory()->count(5)->create([
                'project_id' => $project->id,
                'status_id' => $project->statuses()->where('name', 'In Progress')->first()->id,
            ]);

            ProjectTask::factory()->count(5)->create([
                'project_id' => $project->id,
                'status_id' => $project->statuses()->where('name', 'Done')->first()->id,
            ]);

            $user = User::query()->where('email', 'user@example.com')->first();
            $project->users()->attach($user, ['owner' => true]);
        }
    }
}
