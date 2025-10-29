<?php

namespace App\Domains\Shared\Auth\Services;

use App\Domains\Shared\User\Queries\UserQuery;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(private readonly UserQuery $query) {}

    public function loginByUserModel(User $userModel, Request $request): void
    {
        Auth::login($userModel);
        $request->session()->regenerate();
    }
}
