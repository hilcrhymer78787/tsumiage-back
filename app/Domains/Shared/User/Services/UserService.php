<?php

namespace App\Domains\Shared\User\Services;

use App\Domains\Shared\User\Queries\UserQuery;
use App\Models\User;

class UserService
{
    public function __construct(private readonly UserQuery $query) {}

    public function getUserByEmail(string $email): ?User
    {
        return $this->query->getUserByEmail($email);
    }
}
