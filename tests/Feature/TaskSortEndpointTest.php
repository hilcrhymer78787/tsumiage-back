<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class TaskSortEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクの順番を変更できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク作成（ソート順が0,1,2）
        $tasks = Task::factory()->count(3)->create([
            'task_user_id' => $user->id,
            'task_sort_key' => 0,
        ]);

        // 並び替えのリクエストデータ（ID順を逆にする例）
        $ids = $tasks->pluck('task_id')->reverse()->values()->toArray();

        // POSTリクエスト
        $response = $this->postJson('/api/task/sort', ['ids' => $ids]);

        // レスポンス確認
        $response->assertStatus(200)
            ->assertJson([
                "data" => [
                    'message' => 'タスクの順番を変更しました',
                ]
            ]);

        // DBのタスクの並び順をtask_idの配列で取得
        $dbTaskIds = Task::where('task_user_id', $user->id)
            ->orderBy('task_sort_key')
            ->pluck('task_id')
            ->toArray();

        // DBの並び順確認
        $this->assertEquals($ids, $dbTaskIds);
    }
}
