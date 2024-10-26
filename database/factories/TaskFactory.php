<?php

namespace Database\Factories;

use App\Enums\Tasks\TaskPriorityEnum;
use App\Enums\Tasks\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
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
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(TaskStatusEnum::getValues()),
            'priority' => $this->faker->randomElement(TaskPriorityEnum::getValues()),
            'due_date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'reminder_sent' => false,
            'auto_complete_on_due_date' => false,
        ];
    }
}
