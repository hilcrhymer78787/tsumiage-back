<?php

declare(strict_types=1);

namespace App\Domains\AuthEmailVerify\Services;

use App\Http\Exceptions\AppHttpException;
use Illuminate\Http\Request;

class AuthEmailVerifyService
{
    public function authEmailVerify(Request $request): string
    {
        if ($request->user()->hasVerifiedEmail()) {
            throw new AppHttpException(409, 'すでに認証済みです');
        }

        $request->user()->sendEmailVerificationNotification();
        $user = $request->user();

        return "{$user->email} 宛に認証メールを送信しました。ご確認ください。";
    }
}
