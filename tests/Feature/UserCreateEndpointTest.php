<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserCreateEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function 新規ユーザーを作成できる(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/api/user/create', [
            'name' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'testuser@example.com',
                    'name' => 'testuser',
                    'user_img' => null,
                    'email_verified_at' => null,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'name' => 'testuser',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 同じメールアドレスは登録できない()
    {
        $userArray =[
            'name' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];

        User::create($userArray);

        $response = $this->postJson('/api/user/create', $userArray);

        $response->assertStatus(409)
            ->assertJsonFragment(['emailError' => 'このメールアドレスは既に登録されています']);
    }
}
