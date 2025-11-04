<?php

namespace Tests\Feature;

use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class WorkReadMonthEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function 月間の活動情報を取得できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク作成
        $task1 = $this->createTask($user, ['task_name' => 'タスク1', 'task_sort_key' => 1]);
        $task2 = $this->createTask($user, ['task_name' => 'タスク2', 'task_sort_key' => 2]);

        // 活動情報作成（2025年11月）
        Work::create([
            'work_user_id' => $user->id,
            'work_task_id' => $task1->task_id,
            'work_date' => '2025-11-01',
            'work_state' => 1,
        ]);

        Work::create([
            'work_user_id' => $user->id,
            'work_task_id' => $task2->task_id,
            'work_date' => '2025-11-01',
            'work_state' => 2,
        ]);

        Work::create([
            'work_user_id' => $user->id,
            'work_task_id' => $task1->task_id,
            'work_date' => '2025-11-15',
            'work_state' => 3,
        ]);

        // 他の月のデータ（取得されないはず）
        Work::create([
            'work_user_id' => $user->id,
            'work_task_id' => $task1->task_id,
            'work_date' => '2025-10-15',
            'work_state' => 1,
        ]);

        // API呼び出し
        $response = $this->getJson('/api/work/read/month?user_id=' . $user->id . '&year=2025&month=11');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'calendars' => [
                        '*' => [
                            'date',
                            'tasks' => [
                                '*' => [
                                    'id',
                                    'name',
                                    'createdAt',
                                    'deletedAt',
                                    'work',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        $calendars = $response->json('data.calendars');

        // 30日分のカレンダーが返ることを確認
        $this->assertCount(30, $calendars);

        // 11月1日のデータを確認
        $day1 = collect($calendars)->firstWhere('date', '2025-11-01');
        $this->assertNotNull($day1);
        $this->assertCount(2, $day1['tasks']); // 2つのタスク

        // 11月15日のデータを確認
        $day15 = collect($calendars)->firstWhere('date', '2025-11-15');
        $this->assertNotNull($day15);

        $task1Data = collect($day15['tasks'])->firstWhere('id', $task1->task_id);
        $this->assertEquals(3, $task1Data['work']['state']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function タスクと活動情報がない月でも正しく取得できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // API呼び出し（データなし）
        $response = $this->getJson('/api/work/read/month?user_id=' . $user->id . '&year=2025&month=12');

        $response->assertStatus(200);

        $calendars = $response->json('data.calendars');

        // 31日分のカレンダーが返ることを確認
        $this->assertCount(31, $calendars);

        // 各日にタスクがないことを確認
        foreach ($calendars as $calendar) {
            $this->assertEmpty($calendar['tasks']);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 大量のタスクと活動情報でも正しく取得できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // 50個のタスクを作成
        $tasks = [];
        for ($i = 1; $i <= 50; $i++) {
            $tasks[] = $this->createTask($user, ['task_name' => "タスク{$i}", 'task_sort_key' => $i]);
        }

        // 各タスクに対して月の各日に活動情報を作成（50タスク × 30日 = 1500件）
        foreach ($tasks as $task) {
            for ($day = 1; $day <= 30; $day++) {
                Work::create([
                    'work_user_id' => $user->id,
                    'work_task_id' => $task->task_id,
                    'work_date' => sprintf('2025-11-%02d', $day),
                    'work_state' => ($day % 3) + 1, // 1, 2, 3 をローテーション
                ]);
            }
        }

        // パフォーマンス測定開始
        $startTime = microtime(true);

        // API呼び出し
        $response = $this->getJson('/api/work/read/month?user_id=' . $user->id . '&year=2025&month=11');

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // ミリ秒

        $response->assertStatus(200);

        $calendars = $response->json('data.calendars');

        // 30日分のカレンダーが返ることを確認
        $this->assertCount(30, $calendars);

        // 各日に50個のタスクがあることを確認
        foreach ($calendars as $calendar) {
            $this->assertCount(50, $calendar['tasks']);
        }

        // パフォーマンス検証（1秒以内に完了することを期待）
        $this->assertLessThan(1000, $executionTime, "API実行時間が1秒を超えています: {$executionTime}ms");

        // コンソールに実行時間を出力
        echo "\n大量データでのAPI実行時間: {$executionTime}ms\n";
    }
}
