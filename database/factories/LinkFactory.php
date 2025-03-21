<?php

namespace Database\Factories;

use App\Models\ProjectTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text'          => $this->faker->words(3, true),
            'url'           => $this->faker->url(),
            'linkable_type' => ProjectTask::class,
            'linkable_id'   => function () {
                return ProjectTask::factory()->create()->id;
            },
        ];
    }
}
