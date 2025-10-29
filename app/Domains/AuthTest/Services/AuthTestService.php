<?php

declare(strict_types=1);

namespace App\Domains\AuthTest\Services;

use App\Domains\AuthTest\Queries\AuthTestQuery;
use App\Domains\Shared\Auth\Services\AuthService;
use App\Domains\Shared\LoginInfo\Entities\LoginInfoEntity;
use App\Http\Exceptions\AppHttpException;
use Illuminate\Http\Request;

class AuthTestService
{
    public function __construct(
        private readonly AuthTestQuery $query,
        private readonly AuthService $authService,
    ) {}

    public function testAuth(Request $request): LoginInfoEntity
    {
        $loginInfoModel = $this->query->getTestUserModel();
        if (! $loginInfoModel) {
            throw new AppHttpException(404, 'テストユーザーが見つかりませんでした');
        }

        $this->authService->loginByUserModel($loginInfoModel, $request);

        return new LoginInfoEntity(
            id: $loginInfoModel->id,
            email: $loginInfoModel->email,
            name: $loginInfoModel->name,
            userImg: $loginInfoModel->user_img,
            emailVerifiedAt: $loginInfoModel->email_verified_at,
        );
    }
}
