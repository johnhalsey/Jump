<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\ProjectStatus;
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

        for ($i = 0; $i < 10; $i++) {
            $project = Project::factory()->create();

            $user = User::query()->where('email', 'user0@example.com')->first();
            $otherUsers = User::query()->whereNotIn('email', ['user0@example.com', 'admin@example.com'])->get();

            $project->users()->attach($user, ['owner' => true]);
            foreach ($otherUsers as $user) {
                $project->users()->attach($user, ['owner' => false]);
            }

            foreach ($project->statuses as $status) {
                for ($j = 0; $j < 5; $j++) {
                    ProjectTask::factory()->withNotes()->create([
                        'project_id'  => $project->id,
                        'status_id'   => $status->id,
                        'assignee_id' => $project->users()->inRandomOrder()->first()->id,
                        'creator_id'  => $project->users()->inRandomOrder()->first()->id,
                    ]);
                }
            }

        }
    }
}
