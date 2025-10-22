<?php

namespace App\Domains\UserDelete\Queries;

use App\Models\User;

class UserDeleteQuery
{
    public function deleteUser(int $userId): void
    {
        User::where('id', $userId)->delete();
    }
}
