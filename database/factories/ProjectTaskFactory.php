<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Project;
use App\Models\TaskNote;
use App\Models\ProjectTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectTask>
 */
class ProjectTaskFactory extends Factory
{
    protected $model = ProjectTask::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'project_id'  => function () {
                return Project::factory()->create()->id;
            },
            'assignee_id' => function () {
                return User::factory()->create()->id;
            },
            'creator_id'  => function () {
                return User::factory()->create()->id;
            },
        ];
    }

    public function withNotes(): static
    {
        return $this->afterCreating(function (ProjectTask $task) {
            $random = random_int(3, 20);
            for ($i = 0; $i < $random; $i++) {
                TaskNote::factory()->create([
                    'task_id' => $task->id,
                    'user_id' => $task->project->users()->inRandomOrder()->first()->id,
                ]);
            }

        });
    }


}
