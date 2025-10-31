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
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'email' => 'test@example.com',
                    'name' => 'testuser',
                    'user_img' => null,
                    'email_verified_at' => null,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'testuser',
        ]);
    }

    // #[\PHPUnit\Framework\Attributes\Test]
    // public function ユーザー情報を更新できる(): void
    // {
    //     $user = User::create([
    //         'name' => 'oldname',
    //         'email' => 'test@example.com',
    //         'password' => bcrypt('password'),
    //     ]);

    //     // ここで actingAs すればセッションログイン状態になる
    //     $this->actingAs($user);

    //     // 更新
    //     $response = $this->postJson('/api/user/create', [
    //         'id' => $user->id,
    //         'name' => 'newname',
    //         'email' => 'test@example.com',
    //     ]);

    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'data' => [
    //                 'name' => 'newname',
    //                 'email' => 'test@example.com',
    //             ],
    //         ]);

    //     $this->assertDatabaseHas('users', [
    //         'name' => 'newname',
    //         'email' => 'test@example.com',
    //     ]);
    // }


    #[\PHPUnit\Framework\Attributes\Test]
    public function 同じメールアドレスは登録できない()
    {
        $userArray = [
            'name' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        User::create($userArray);

        $response = $this->postJson('/api/user/create', $userArray);

        $response->assertStatus(409)
            ->assertJsonFragment(['emailError' => 'このメールアドレスは既に登録されています']);
    }
}
