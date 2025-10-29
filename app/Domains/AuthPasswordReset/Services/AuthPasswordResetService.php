<?php

declare(strict_types=1);

namespace App\Domains\AuthPasswordReset\Services;

use App\Domains\AuthBasic\Parameters\AuthBasicParameter;
use App\Domains\AuthBasic\Queries\AuthBasicQuery;
use App\Domains\AuthPasswordReset\Parameters\AuthPasswordResetParameter;
use App\Domains\Shared\LoginInfo\Entities\LoginInfoEntity;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Http\Exceptions\AppHttpException;
use App\Http\Requests\AuthPasswordResetRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthPasswordResetService
{
    public function __construct(
        private readonly LoginInfoService $loginInfoService,
        private readonly AuthBasicQuery $query,
    ) {}

    public function passwordReset(AuthPasswordResetParameter $params, AuthPasswordResetRequest $request): LoginInfoEntity
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );
        if ($status !== Password::PASSWORD_RESET) throw new AppHttpException(500, 'パスワードリセットに失敗しました。');

        $loginInfoModel = $this->query->getLoginInfo(new AuthBasicParameter(
            email: $params->email,
            password: $params->password
        ));
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
