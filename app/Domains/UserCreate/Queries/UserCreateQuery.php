<?php

namespace App\Domains\UserCreate\Queries;

use App\Domains\UserCreate\Parameters\UserCreateParameter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserCreateQuery
{
    /**
     * ユーザー作成
     */
    public function createUser(UserCreateParameter $params): ?User
    {
        return User::create([
            'name'     => $params->name,
            'email'    => $params->email,
            'password' => Hash::make($params->password),
            'user_img' => $params->userImg,
        ]);
    }
    

    /**
     * ユーザー更新（パスワード以外）
     */
    public function updateUser(UserCreateParameter $params, User $loginInfoModel): void
    {
        User::where('id', $loginInfoModel->id)->update([
            'name'     => $params->name,
            'email'    => $params->email,
            'user_img' => $params->userImg,
        ]);
    }

    /**
     * パスワード更新
     */
    public function updatePassword(UserCreateParameter $params, User $loginInfoModel): void
    {
        User::where('id', $loginInfoModel->id)->update([
            'password' => Hash::make($params->password), // 🔒 ハッシュ化
        ]);
    }
}
