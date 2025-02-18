<?php

namespace Database\Seeders;

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

        $project = Project::factory()->create();

        foreach(DefaultProjectStatus::cases() as $status) {
            ProjectStatus::create([
                'name' => $status->value,
                'project_id' => $project->id,
            ]);
        }

        $user = User::query()->where('email', 'user@example.com')->first();
        $project->users()->attach($user, ['owner' => true]);
    }
}
