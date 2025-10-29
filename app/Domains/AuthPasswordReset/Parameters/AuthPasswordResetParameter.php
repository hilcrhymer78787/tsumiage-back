<?php

namespace App\Domains\AuthPasswordReset\Parameters;

readonly class AuthPasswordResetParameter
{
    public function __construct(
        public string $email,
        public string $token,
        public string $password,
    ) {}

    public static function makeParams(array $validated): self
    {
        return new self(
            email: $validated['email'],
            token: $validated['token'],
            password: $validated['password'],
        );
    }
}
