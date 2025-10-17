<?php

namespace App\Domains\Shared\LoginInfo\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LoginInfoService
{
    public function getLoginInfo(): ?User
    {
        return Auth::user();
    }
}
