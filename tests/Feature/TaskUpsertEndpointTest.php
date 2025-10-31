<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskUpsertEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクを新規作成できる(): void
    {
        // ユーザー作成＆ログイン
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $response = $this->postJson('/api/task/create', [
            'name' => '新しいタスク',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => 'タスクを作成しました'],
            ]);

        $this->assertDatabaseHas('tasks', [
            'task_name' => '新しいタスク',
            'task_user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 既存タスクを更新できる(): void
    {
        // ユーザー作成＆ログイン
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        // タスク作成
        $task = Task::create([
            'task_name' => '古いタスク',
            'task_user_id' => $user->id,
        ]);

        // タスク更新
        $response = $this->postJson('/api/task/create', [
            'id' => $task->task_id,
            'name' => '更新後タスク',
        ]);

        // レスポンス確認
        $response->assertStatus(200)->assertJson([
            'status' => 200,
            'data' => ['message' => 'タスクを更新しました'],

        ]);

        // DB確認
        $this->assertDatabaseHas('tasks', [
            'task_id' => $task->task_id,
            'task_name' => '更新後タスク',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 存在しないタスクを更新しようとするとエラーになる(): void
    {
        // ユーザー作成＆ログイン
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $response = $this->postJson('/api/task/create', [
            'id' => 9999, // 存在しないID
            'name' => '更新タスク',
        ]);

        $response->assertStatus(404)->assertJson([
            'status' => 404,
            'message' => '更新するタスクが存在しません',
            'data' => null,
        ]);
    }
}
