<?php

declare(strict_types=1);

namespace App\Domains\AuthLogout\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthLogoutService
{
    public function logout(Request $request): string
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return "ログアウトしました";
    }
};
