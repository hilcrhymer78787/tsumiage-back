<?php

declare(strict_types=1);

namespace App\Domains\Shared\CheckIsExistEmail\Services;

use App\Domains\Shared\CheckIsExistEmail\Queries\CheckIsExistEmailQuery;

class CheckIsExistEmailService
{
    public function __construct(
        private readonly CheckIsExistEmailQuery $query,
    ) {}

    public function checkIsExistEmail(string $email): bool
    {
        return $this->query->checkIsExistEmail($email)->exists();
    }
}
