<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class TaskDeleteEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクを論理削除できる(): void
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
            'task_name' => 'タスク',
            'task_user_id' => $user->id,
        ]);

        // タスク削除
        $response = $this->deleteJson('/api/task/delete', [
            'id' => $task->task_id,
            'is_hard_delete' => false,
        ]);

        // レスポンス確認
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => 'タスクを削除しました'],
            ]);

        // DB確認
        $this->assertSoftDeleted('tasks', [
            'task_name' => 'タスク',
            'task_user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクを物理削除できる(): void
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
            'task_name' => 'タスク',
            'task_user_id' => $user->id,
        ]);

        // タスク削除
        $response = $this->deleteJson('/api/task/delete', [
            'id' => $task->task_id,
            'is_hard_delete' => true,
        ]);

        // レスポンス確認
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => 'タスクを削除しました'],
            ]);

        // DB確認
        $this->assertDatabaseMissing('tasks', [
            'task_name' => 'タスク',
            'task_user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクが存在しません(): void
    {
        // ユーザー作成＆ログイン
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        // タスク削除
        $response = $this->deleteJson('/api/task/delete', [
            'id' => 99999,
            'is_hard_delete' => false,
        ]);

        $response->assertStatus(404)->assertJson([
            'status' => 404,
            'message' => 'タスクが存在しません',
            'data' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 自分のタスク以外を削除することはできません(): void
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
            'task_name' => 'タスク',
            'task_user_id' => 99999,
        ]);

        // タスク削除
        $response = $this->deleteJson('/api/task/delete', [
            'id' => $task->task_id,
            'is_hard_delete' => false,
        ]);

        $response->assertStatus(403)->assertJson([
            'status' => 403,
            'message' => '自分のタスク以外を削除することはできません',
            'data' => null,
        ]);
    }
}
