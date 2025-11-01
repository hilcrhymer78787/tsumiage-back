<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\FeatureTestCase;

class AuthPasswordForgotEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function パスワードリセットメール送信成功(): void
    {
        Mail::shouldReceive('send')
            ->once()
            ->andReturnTrue();

        // テスト用ユーザー作成
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->postJson('/api/user/auth/password/forgot', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'data' => [
                    'message' => 'test@example.com 宛にパスワードリセットメールを送信しました。ご確認ください。',
                ],
            ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 存在しないメールアドレスで送信失敗(): void
    {
        $response = $this->postJson('/api/user/auth/password/forgot', [
            'email' => 'notfound@example.com',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status' => 404,
                'data' => [
                    'emailError' => 'このメールアドレスは登録されていません',
                ],
            ]);
    }
}
