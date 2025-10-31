<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthBasicEndpointTest extends TestCase
{
    use RefreshDatabase;

    // public function test_basic_auth_success(): void
    // {
    //     // 1. ユーザー作成
    //     $user = User::create([
    //         'id' => 1,
    //         'email' => 'test@example.com',
    //         'name' => 'Test User',
    //         'user_img' => null,
    //         'password' => bcrypt('password'), // ← ここを追加
    //     ]);

    //     // 2. 正しいメール・パスワードでPOST
    //     $response = $this->postJson('/api/user/auth/basic', [
    //         'email' => 'test@example.com',
    //         'password' => 'password',
    //     ]);

    //     // 3. 検証
    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'data' => [
    //                 'id' => $user->id,
    //                 'email' => $user->email,
    //                 'name' => $user->name,
    //                 'user_img' => $user->user_img,
    //                 'email_verified_at' => $user->email_verified_at,
    //             ],
    //         ]);
    // }

    // public function test_basic_auth_email_not_found(): void
    // {
    //     // ユーザー未登録状態で呼び出す
    //     $response = $this->postJson('/api/user/auth/basic', [
    //         'email' => 'notfound@example.com',
    //         'password' => 'password',
    //     ]);

    //     $response->assertStatus(404)
    //         ->assertJson([
    //             'status' => 404,
    //             'data' => null,
    //         ])
    //         ->assertJsonPath('detail.emailError', 'このメールアドレスは登録されていません');
    // }

    // public function test_basic_auth_password_incorrect(): void
    // {
    //     // 1. 正しいメール、違うパスワード
    //     User::factory()->create([
    //         'email' => 'test@example.com',
    //         'password' => Hash::make('correct-password'),
    //     ]);

    //     // 2. 間違ったパスワードでPOST
    //     $response = $this->postJson('/api/user/auth/basic', [
    //         'email' => 'test@example.com',
    //         'password' => 'wrong-password',
    //     ]);

    //     // 3. 検証
    //     $response->assertStatus(500)
    //         ->assertJson([
    //             'status' => 500,
    //             'data' => null,
    //         ])
    //         ->assertJsonPath('detail.passwordError', 'パスワードが間違っています');
    // }
}
