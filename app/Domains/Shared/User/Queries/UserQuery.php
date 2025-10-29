<?php

namespace App\Domains\Shared\User\Queries;

use App\Models\User;

class UserQuery
{
    public function getUserByEmail($email): ?User
    {
        return User::where('email', $email)->first();
    }
}
