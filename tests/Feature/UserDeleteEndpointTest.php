<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeleteEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function ユーザーを削除しました(): void
    {
        // ユーザー作成＆ログイン
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        // APIリクエスト
        $response = $this->deleteJson('/api/user/delete');

        // レスポンス確認
        $response->assertStatus(200)->assertJson([
            'status' => 200,
            'data' => ['message' => 'ユーザーを削除しました'],
        ]);

        //DB確認
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function ユーザーを削除できませんでした(): void
    {
        // ユーザー作成＆ログイン
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        // ユーザー削除
        $user = User::where('id', $user->id)->delete();

        // APIリクエスト
        $response = $this->deleteJson('/api/user/delete');

        // レスポンス確認
        $response->assertStatus(500)
            ->assertJson([
                'status' => 500,
                'message' => 'ユーザーを削除できませんでした',
            ]);
    }
}
