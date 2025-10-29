<?php

declare(strict_types=1);

namespace App\Domains\AuthBasic\Services;

use App\Domains\AuthBasic\Parameters\AuthBasicParameter;
use App\Domains\AuthBasic\Queries\AuthBasicQuery;
use App\Domains\Shared\LoginInfo\Entities\LoginInfoEntity;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Http\Exceptions\AppHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthBasicService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
        private readonly AuthBasicQuery $query,
    ) {}

    /**
     * メールアドレス・パスワード認証
     */
    public function basicAuth(AuthBasicParameter $params, Request $request): LoginInfoEntity
    {
        $loginInfoModel = $this->query->getLoginInfo($params);

        if (! $loginInfoModel) {
            throw new AppHttpException(404, '', ['emailError' => 'このメールアドレスは登録されていません']);
        }

        $isCorrect = Hash::check($params->password, $loginInfoModel->password);
        if (! $isCorrect) {
            throw new AppHttpException(500, '', ['passwordError' => 'パスワードが間違っています']);
        }

        // TODO 統一
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
}
