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
        $tasks = collect([
            Task::create([
                'task_name' => 'タスク1',
                'task_user_id' => $user->id,
                'task_sort_key' => 0,
            ]),
            Task::create([
                'task_name' => 'タスク2',
                'task_user_id' => $user->id,
                'task_sort_key' => 1,
            ]),
            Task::create([
                'task_name' => 'タスク3',
                'task_user_id' => $user->id,
                'task_sort_key' => 2,
            ]),
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

        // DBの並び順確認
        $this->assertEquals(
            $ids[0],
            Task::where('task_user_id', $user->id)->orderBy('task_sort_key')->first()->task_id
        );
        $this->assertEquals(
            $ids[1],
            Task::where('task_user_id', $user->id)->orderBy('task_sort_key')->skip(1)->first()->task_id
        );
        $this->assertEquals(
            $ids[2],
            Task::where('task_user_id', $user->id)->orderBy('task_sort_key')->skip(2)->first()->task_id
        );
    }
}
