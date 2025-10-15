<?php

namespace App\Domains\Shared\LoginInfo\Queries;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginInfoQuery
{
    public function getLoginInfo(Request $request): ?User
    {
        $user = JWTAuth::parseToken()->authenticate();
        if (!$user) return null;
        return $user->makeHidden(['password', 'remember_token']);
    }
}
