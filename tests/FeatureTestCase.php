<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    protected ?User $user = null;

    /**
     * テスト用ユーザーを Factory で作成してログインする
     */
    protected function actingAsUser(array $overrides = []): User
    {
        // Factoryを使ってUser生成
        $this->user = User::factory()->create($overrides);

        // ログイン状態に設定
        $this->actingAs($this->user);

        return $this->user;
    }
}
