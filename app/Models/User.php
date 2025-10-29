<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'user_img',
        'token',
    ];

    protected $hidden = [
        'password',
    ];

    public function sendEmailVerificationNotification()
    {
        $this->notify(new class extends VerifyEmailBase {
            protected function verificationUrl($notifiable)
            {
                // Laravel署名付きURL（必要ならAPIで確認）
                $signedUrl = URL::temporarySignedRoute(
                    'verification.verify', // Laravel API側のルート名
                    now()->addMinutes(60),
                    ['id' => $notifiable->id, 'hash' => sha1($notifiable->email)]
                );

                // Next.js 側URLに置き換え
                $nextUrl = "http://localhost:3000/email/verify/{$notifiable->id}/" . sha1($notifiable->email);

                return $nextUrl;
            }

            public function toMail($notifiable)
            {
                return (new MailMessage)
                    ->subject('メールアドレスの確認')
                    ->line('下のボタンをクリックしてメールアドレスを確認してください。')
                    ->action('メール認証', $this->verificationUrl($notifiable));
            }
        });
    }
}
