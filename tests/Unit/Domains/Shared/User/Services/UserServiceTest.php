<?php

namespace Tests\Unit\Domains\Shared\User\Services;

use App\Domains\Shared\User\Services\UserService;
use App\Domains\Shared\User\Queries\UserQuery;
use App\Models\User;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private UserQuery&MockObject $query;
    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // UserQuery をモック化
        $this->query = $this->createMock(UserQuery::class);

        // モックを UserService に注入
        $this->service = new UserService($this->query);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function メールアドレスからユーザーを取得できる(): void
    {
        // 取得したいユーザーのemail
        $email = 'test@example.com';
        $user = new User([
            'id' => 1,
            'email' => $email,
            'name' => 'Test User',
        ]);

        // query->getUserByEmail メソッドのモック動作を設定
        $this->query->expects($this->once())
            ->method('getUserByEmail')
            ->with($email)
            ->willReturn($user);

        // テストしたい関数の実行
        $result = $this->service->getUserByEmail($email);

        // 関数の戻り値の型が「User」であることの確認
        $this->assertInstanceOf(User::class, $result);

        // 関数の戻り値のemailが「取得したいユーザーのemail」であることの確認
        $this->assertSame($email, $result->email);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function 存在しないメールアドレスならnullを返す(): void
    {
        // 取得したいユーザーのemail
        $email = 'notfound@example.com';

        // query->getUserByEmail メソッドのモック動作を設定
        $this->query->expects($this->once())
            ->method('getUserByEmail')
            ->with($email)
            ->willReturn(null);

        // テストしたい関数の実行
        $result = $this->service->getUserByEmail($email);

        // 関数の戻り値が「null」であることの確認
        $this->assertNull($result);
    }
}
