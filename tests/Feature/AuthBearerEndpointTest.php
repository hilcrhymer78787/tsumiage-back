<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\FeatureTestCase;

class AuthBearerEndpointTest extends FeatureTestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function ベアラー認証成功(): void
    {
        // ユーザー作成＆ログイン（Factory利用）
        $user = $this->actingAsUser();

        $response = $this->getJson('/api/user/auth/bearer');
        dump($response->json());

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

    #[\PHPUnit\Framework\Attributes\Test]
    public function 認証失敗(): void
    {
        $response = $this->getJson('/api/user/auth/bearer');

        $response->assertStatus(500)
            ->assertJson([
                'status' => 500,
                'message' => 'Unauthenticated.',
            ]);
    }
}
