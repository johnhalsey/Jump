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
            TaskNote::factory()->count(random_int(3, 20))->create([
                'task_id' => $task->id,
                'user_id' => $task->assignee_id,
            ]);
        });
    }


}
