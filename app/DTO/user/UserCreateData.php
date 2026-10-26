<?php

namespace App\DTO\user;

use App\Enums\UserTypeEnum;

final readonly class UserCreateData
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $password,
        public UserTypeEnum $type = UserTypeEnum::NORMAL,
    ) {
    }
}
