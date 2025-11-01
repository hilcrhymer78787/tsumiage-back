<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class TaskRestoreEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクを復元できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク作成
        $task = $this->createTask($user);

        // タスク論理削除
        Task::where('task_id', $task->task_id)->delete();

        // タスク復元
        $response = $this->postJson('/api/task/restore', ['id' => $task->task_id]);

        // レスポンス確認
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => 'タスクを復元しました'],
            ]);

        // DB確認
        $this->assertDatabaseHas('tasks', [
            'task_name' => $task->task_name,
            'task_user_id' => $user->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクが存在しません(): void
    {
        // ユーザー作成＆ログイン
        $this->actingAsUser();

        // タスク削除
        $response = $this->deleteJson('/api/task/delete', [
            'id' => 99999,
            'is_hard_delete' => false,
        ]);

        // レスポンス確認
        $response->assertStatus(404)->assertJson([
            'status' => 404,
            'message' => 'タスクが存在しません',
            'data' => null,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 自分のタスク以外を復元することはできません(): void
    {
        // ユーザー作成＆ログイン
        $this->actingAsUser();

        // タスク作成
        $task = $this->createTask(null, ['task_user_id' => 99999]);

        // タスク削除
        $response = $this->postJson('/api/task/restore', [
            'id' => $task->task_id,
            'is_hard_delete' => false,
        ]);

        // レスポンス確認
        $response->assertStatus(403)->assertJson([
            'status' => 403,
            'message' => '自分のタスク以外を復元することはできません',
            'data' => null,
        ]);
    }
}
