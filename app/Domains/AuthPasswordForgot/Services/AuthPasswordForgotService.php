<?php

declare(strict_types=1);

namespace App\Domains\AuthPasswordForgot\Services;

use App\Domains\AuthPasswordForgot\Parameters\AuthPasswordForgotParameter;
use App\Domains\Shared\LoginInfo\Services\LoginInfoService;
use App\Http\Exceptions\AppHttpException;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class AuthPasswordForgotService
{
    public function __construct(private readonly LoginInfoService $loginInfoService) {}

    public function passwordForgot(AuthPasswordForgotParameter $params): string
    {
        // メールアドレスが存在するか確認
        $targetUser = User::where('email', $params->email)->first();
        if (! $targetUser) {
            throw new AppHttpException(404, '', ['emailError' => 'このメールアドレスは登録されていません']);
        }

        // パスワードリセットリンクを作成
        $status = Password::sendResetLink(
            ['email' => $targetUser->email],
            function ($user, $token) {
                $resetUrl = config('app.frontend_url')."/reset-password?token={$token}&email={$user->email}";
                Mail::send([], [], function ($message) use ($user, $resetUrl) {
                    $message->to($user->email)
                        ->subject('パスワードリセットのご案内')
                        ->text("以下のリンクからパスワードをリセットしてください。\n\n".$resetUrl);
                });
            }
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw new AppHttpException(500, 'メール送信に失敗しました');
        }

        return "{$params->email} 宛にパスワードリセットメールを送信しました。ご確認ください。";
    }
}
