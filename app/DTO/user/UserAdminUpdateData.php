<?php

namespace App\DTO\user;

use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;

final readonly class UserAdminUpdateData
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phone = null,
        public ?UserTypeEnum $type = null,
        public ?GlobalStatusEnum $status = null,
        public ?UserProfileUpdateData $profile = null,
    ) {
    }

    /** @return array<string, string|UserTypeEnum|GlobalStatusEnum> */
    public function toModelAttributes(): array
    {
        return array_filter([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'type' => $this->type,
            'status' => $this->status,
        ], static fn (string|UserTypeEnum|GlobalStatusEnum|null $value): bool => $value !== null);
    }
}
