<?php

namespace App\Models;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;

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
        $this->notify(new class extends VerifyEmailBase
        {
            protected function verificationUrl($notifiable)
            {
                $params = http_build_query([
                    'id' => $notifiable->id,
                    'hash' => sha1($notifiable->email),
                ]);
                return config('app.frontend_url') . "/email?{$params}";
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
