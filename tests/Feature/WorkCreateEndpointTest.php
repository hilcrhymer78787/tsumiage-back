<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class WorkCreateEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function 活動情報を新規作成できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク作成
        $task = Task::create([
            'task_name' => 'タスク',
            'task_user_id' => $user->id,
        ]);

        $response = $this->postJson('/api/work/create', [
            'task_id' => $task->task_id,
            'date' => date('Y-m-d'),
            'state' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => '活動情報を更新しました'],
            ]);

        $this->assertDatabaseHas('works', [
            'work_user_id' => $user->id,
            'work_task_id' => $task->task_id,
            'work_state' => 1,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 既存活動情報を更新できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク作成
        $task = Task::create([
            'task_name' => 'タスク',
            'task_user_id' => $user->id,
        ]);

        // 活動情報作成
        $work = Work::create([
            'work_user_id' => $user->id,
            'work_task_id' => $task->task_id,
            'work_date' => date('Y-m-d'),
            'work_state' => 1,
        ]);

        // 活動情報更新
        $response = $this->postJson('/api/work/create', [
            'task_id' => $task->task_id,
            'date' => date('Y-m-d'),
            'state' => 2,
        ]);

        // レスポンス確認
        $response->assertStatus(200)->assertJson([
            'status' => 200,
            'data' => ['message' => '活動情報を更新しました'],

        ]);

        // DB確認
        $this->assertDatabaseHas('works', [
            'work_id' => $work->work_id,
            'work_user_id' => $user->id,
            'work_task_id' => $task->task_id,
            'work_state' => 2,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 存在しないタスクの活動情報を更新しようとするとエラーになる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // 活動情報更新
        $response = $this->postJson('/api/work/create', [
            'task_id' => 99999,
            'date' => date('Y-m-d'),
            'state' => 2,
        ]);

        $response->assertStatus(404)->assertJson([
            'status' => 404,
            'message' => '更新するタスクが存在しません',
            'data' => null,
        ]);
    }
}
