<?php

namespace App\Domains\AuthTest\Queries;

use App\Models\User;

class AuthTestQuery
{
    public function getTestUserModel(): ?User
    {
        return User::find(1);
    }
}
