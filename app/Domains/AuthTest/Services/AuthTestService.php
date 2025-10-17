<?php

declare(strict_types=1);

namespace App\Domains\AuthTest\Services;

use App\Domains\Shared\LoginInfo\Entities\LoginInfoEntity;
use App\Domains\AuthTest\Queries\AuthTestQuery;
use App\Http\Exceptions\AppHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthTestService
{
    public function __construct(
        private readonly AuthTestQuery $query,
    ) {}

    public function getLoginInfoEntity(Request $request): LoginInfoEntity
    {
        $loginInfoModel = $this->query->getLoginInfoModel();
        if (!$loginInfoModel) throw new AppHttpException(404, 'テストユーザーが見つかりませんでした');

        Auth::login($loginInfoModel);
        $request->session()->regenerate();

        return new LoginInfoEntity(
            id: $loginInfoModel->id,
            email: $loginInfoModel->email,
            name: $loginInfoModel->name,
            userImg: $loginInfoModel->user_img,
            emailVerifiedAt: $loginInfoModel->email_verified_at,
        );
    }
};
