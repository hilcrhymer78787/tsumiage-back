<?php

declare(strict_types=1);

namespace App\Domains\AuthPasswordForgot\Services;

use App\Domains\AuthPasswordForgot\Parameters\AuthPasswordForgotParameter;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Http\Exceptions\AppHttpException;
use App\Models\User;
use Illuminate\Support\Facades\Password;

class AuthPasswordForgotService
{
    public function __construct(private readonly LoginInfoService $loginInfoService) {}

    public function passwordForgot(AuthPasswordForgotParameter $params): string
    {
        // メールアドレスが存在するか確認
        // TODO
        $targetUser = User::where('email', $params->email)->first();
        if (! $targetUser) {
            throw new AppHttpException(404, '', ['emailError' => 'このメールアドレスは登録されていません']);
        }

        $status = Password::sendResetLink(['email' => $params->email]);

        // 連続送信（スパム防止）の場合
        if ($status === Password::RESET_THROTTLED) {
            throw new AppHttpException(429, '短時間に連続でメール送信が行われたため、しばらく時間をおいてお試しください。');
        }

        if ($status !== Password::RESET_LINK_SENT) {
            throw new AppHttpException(500, 'メール送信に失敗しました');
        }

        return "{$params->email} 宛にパスワードリセットメールを送信しました。ご確認ください。";
    }
}
