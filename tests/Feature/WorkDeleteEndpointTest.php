<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class WorkDeleteEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function 活動情報を論理削除できる(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク作成
        $task = $this->createTask($user);

        // 活動情報作成
        $work = $this->createWork($task, $user);

        $response = $this->deleteJson('/api/work/delete', ['id' => $work->work_id]);

        // レスポンス確認
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => ['message' => '活動情報を削除しました'],
            ]);

        // DB確認
        $this->assertSoftDeleted('works', ['work_id' => $work->work_id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 活動情報が存在しません(): void
    {
        // ユーザー作成＆ログイン
        $user = $this->actingAsUser();

        // タスク削除
        $response = $this->deleteJson('/api/work/delete', ['id' => 99999]);

        // レスポンス確認
        $response->assertStatus(404)->assertJson([
            'status' => 404,
            'message' => '活動情報が存在しません',
            'data' => null,
        ]);
    }
}
