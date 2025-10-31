<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthBearerEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function ベアラー認証成功(): void
    {
        // ユーザー作成＆ログイン
        $user = User::create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($user);

        $response = $this->getJson('/api/user/auth/bearer');

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
