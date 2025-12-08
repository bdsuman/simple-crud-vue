<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'author_name' => $this->faker->name(),
            'job_title' => $this->faker->jobTitle(),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraph(),
            'rating' => $this->faker->numberBetween(3, 5),
            'avatar' => $this->faker->imageUrl(100, 100, 'people'),
            'publish' => true,
        ];
    }
}
