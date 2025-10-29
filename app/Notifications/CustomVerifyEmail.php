<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    protected function verificationUrl($notifiable)
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify', // Laravel側のAPIルート名
            now()->addMinutes(60),
            ['id' => $notifiable->id, 'hash' => sha1($notifiable->email)]
        );

        // Next.js側URLに置き換え
        $nextUrl = "http://localhost:3000/email/verify/{$notifiable->id}/".sha1($notifiable->email);

        return $nextUrl;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('メールアドレスの確認')
            ->line('下のボタンをクリックしてメールアドレスを確認してください。')
            ->action('メール認証', $this->verificationUrl($notifiable));
    }
}
