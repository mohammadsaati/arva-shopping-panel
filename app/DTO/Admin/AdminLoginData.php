<?php

namespace App\DTO\Admin;

final readonly class AdminLoginData
{
    public function __construct(
        public string $email,
        public string $password,
    ) {
    }
}
