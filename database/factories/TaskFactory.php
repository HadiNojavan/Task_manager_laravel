<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->sentence(),
            'title' => $this->faker->sentence(4),
            'status' => $this->faker->randomElement([
                'pending',
                'completed',
                'incomplete',
            ]),
            'priority' => $this->faker->randomElement([
                'low',
                'medium',
                'high',
            ]),
            'due_date' => $this->faker->dateTime(),
            'category_id' => Category::factory(),
        ];
    }
}
