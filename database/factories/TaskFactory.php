<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'task_name' => $this->faker->sentence(3),
            'task_user_id' => 1,
            'task_sort_key' => 0,
        ];
    }
}
