<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\FeatureTestCase;

class AuthEmailVerifySendEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function 未認証ユーザーは認証メールを送信できる(): void
    {
        $user = $this->actingAsUser();
        Notification::fake();

        $response = $this->postJson('/api/user/auth/email/verify/send');

        $response->assertOk()
            ->assertJson([
                'status' => 200,
                'data' => [
                    'message' => "{$user->email} 宛に認証メールを送信しました。ご確認ください。",
                ],
            ]);

        // TODO
        // VerifyEmail通知が送信されたことを確認
        // Notification::assertSentTo($user, VerifyEmail::class);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 認証済みユーザーが再送信すると409エラーになる(): void
    {
        $this->actingAsUser(['email_verified_at' => now()]);

        $response = $this->postJson('/api/user/auth/email/verify/send');

        $response->assertStatus(409)
            ->assertJson([
                'status' => 409,
                'message' => 'すでに認証済みです',
            ]);
    }
}
