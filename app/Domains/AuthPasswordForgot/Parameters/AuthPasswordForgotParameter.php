<?php

namespace App\Domains\AuthPasswordForgot\Parameters;

readonly class AuthPasswordForgotParameter
{
    public function __construct(
        public string $email,
    ) {}

    public static function makeParams(array $validated): self
    {
        return new self(
            email: $validated['email'],
        );
    }
}
