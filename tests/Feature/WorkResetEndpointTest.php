<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class WorkResetEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function 活動情報を論理削除できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク作成
        $task = $this->createTask($user, [
            'created_at' => date('Y-m-d', strtotime('-3 days')),
        ]);

        // 活動情報作成
        $work = $this->createWork($task, $user, [
            'created_at' => date('Y-m-d', strtotime('-2 days')),
        ]);

        $response = $this->deleteJson('/api/work/reset');

        // レスポンス確認
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => '活動情報を全て削除しました'],
            ]);

        // DB確認
        $this->assertDatabaseMissing('works', ['work_id' => $work->work_id]);

        // タスクのcreated_atが本日に更新されているか確認
        $this->assertTrue(
            Task::where('task_id', $task->task_id)
                ->whereDate('created_at', date('Y-m-d'))
                ->exists()
        );
    }
}
