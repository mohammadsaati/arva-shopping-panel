<?php

namespace App\DTO\user;

final readonly class UserPasswordChangeData
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
    ) {
    }
}
