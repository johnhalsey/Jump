<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\ProjectTask;
use Illuminate\Console\View\Components\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskNote>
 */
class TaskNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_id' => function () {
                return ProjectTask::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'note' => $this->faker->sentence(),
        ];
    }
}
