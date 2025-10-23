<?php

namespace App\Domains\EmailVerifyIdHash\Services;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerifyIdHashService
{
    public function emailVerify(EmailVerificationRequest $request): string
    {
        $request->fulfill();

        return 'メール認証が完了しました';
    }
}
