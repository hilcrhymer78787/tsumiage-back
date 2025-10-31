<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthBasicEndpointTest extends TestCase
{
    use RefreshDatabase;

    // 成功
    public function test_basic_auth_success(): void
    {
        // 1. ユーザー作成
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'), // ← ここを追加
        ]);

        // 2. 正しいメール・パスワードでPOST
        $response = $this->postJson('/api/user/auth/basic', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // 3. 検証
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

    // このメールアドレスは登録されていません
    public function test_basic_auth_email_not_found(): void
    {
        $response = $this->postJson('/api/user/auth/basic', [
            'email' => 'notfound@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 404,
                'data' => [
                    'emailError' => 'このメールアドレスは登録されていません',
                ],
            ]);
    }

    // パスワードが間違っています
    public function test_basic_auth_password_incorrect(): void
    {
        // 1. 正しいメール、違うパスワード
        User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => Hash::make('correct-password'),
        ]);

        // 2. 間違ったパスワードでPOST
        $response = $this->postJson('/api/user/auth/basic', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // 3. 検証
        $response->assertStatus(500)
            ->assertJson([
                'status' => 500,
                'data' => [
                    'passwordError' => 'パスワードが間違っています',
                ],
            ]);
    }

    // バリデーションエラーが発生しました。
    public function test_basic_auth_email_invalid_format(): void
    {
        // 不正なメールアドレス形式
        $response = $this->postJson('/api/user/auth/basic', [
            'email' => 'invalid-email-format',
            'password' => 'password',
        ]);

        // 検証
        $response->assertStatus(422)
            ->assertJson([
                'status' => 422,
                'message' => 'バリデーションエラーが発生しました。',
                'data' => [
                    'errors' => [
                        'email' => [
                            'email はメールアドレス形式でなければなりません。'
                        ],
                    ],
                ],
            ]);
    }
}
