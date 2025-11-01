<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class TaskReadEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクを取得できる(): void
    {
        $user = $this->actingAsUser();

        $tasks = Task::factory()->count(3)->create([
            'task_user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/task/read?'.http_build_query([
            'is_only_trashed' => false,
            'user_id' => $user->id,
            'date' => date('Y-m-d'),
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'date' => date('Y-m-d'),
                    'tasks' => $tasks->map(fn ($task) => [
                        'id' => $task->task_id,
                        'name' => $task->task_name,
                        'createdAt' => $task->created_at,
                        'deletedAt' => null,
                        'work' => [
                            'id' => 0,
                            'date' => '',
                            'taskId' => 0,
                            'userId' => 0,
                            'state' => 0,
                        ],
                    ])->toArray(),
                ],
                'status' => 200,
            ]);
    }
}
