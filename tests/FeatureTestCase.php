<?php

namespace Tests;

use App\Models\Task;
use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected ?User $user = null;

    /**
     * テスト用ユーザーを作成してログインする
     */
    protected function actingAsUser(array $overrides = []): User
    {
        $this->user = User::factory()->create(array_merge([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ], $overrides));

        $this->actingAs($this->user);

        return $this->user;
    }

    /**
     * テスト用タスクを作成する
     */
    protected function createTask(?User $user, ?array $overrides = []): Task
    {
        $user ??= $this->user ?? $this->actingAsUser();

        return Task::factory()->create(array_merge([
            'task_user_id' => $user->id,
        ], $overrides));
    }

    /**
     * テスト用活動情報を作成する
     */
    protected function createWork(Task $task, User $user, ?array $overrides = []): Work
    {
        return Work::create(array_merge([
            'work_date' => date('Y-m-d'),
            'work_task_id' => $task->task_id,
            'work_user_id' => $user->id,
            'created_at' => date('Y-m-d'),
            'work_state' => 1,
        ], $overrides));
    }
}
