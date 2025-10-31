<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTestEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_login_info_when_user_exists(): void
    {
        // 1. テストユーザー作成
        $user = User::create([
            'id' => 1,
            'email' => 'test@example.com',
            'name' => 'Test User',
            'user_img' => null,
            'password' => bcrypt('password'), // ← ここを追加
        ]);

        // 2. エンドポイント呼び出し
        $response = $this->getJson('/api/user/auth/test');

        // 3. ステータスと JSON 内容確認
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'user_img' => $user->user_img,
                    'email_verified_at' => $user->email_verified_at,
                ],
            ]);
    }

    public function test_returns_error_when_user_not_found(): void
    {
        // テストユーザーを作らずに呼ぶ
        $response = $this->getJson('/api/user/auth/test');

        // ステータスコードとJSON内容を検証
        $response->assertStatus(404)
            ->assertJsonFragment([
                'status' => 404,
                'message' => 'テストユーザーが見つかりませんでした',
                'data' => null,
            ]);
    }
}
