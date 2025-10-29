<?php

namespace App\Http\Requests;

use App\Http\Requests\Base\BaseFormRequest;

class AuthPasswordResetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'email は必須です。',
            'email.email' => 'email はメールアドレス形式でなければなりません。',
            'email.max' => 'email は255文字以内で入力してください。',
            'token.required' => 'トークンは必須です。',
            'password.required' => 'パスワードは必須です。',
            'password.confirmed' => 'パスワード確認が一致しません。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
        ];
    }
}
