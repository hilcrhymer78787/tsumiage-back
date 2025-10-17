<?php

namespace App\Domains\AuthTest\Queries;

use App\Models\User;

class AuthTestQuery
{
    public function getTestUserModel(): User | null
    {
        return User::find(1);
    }
}
