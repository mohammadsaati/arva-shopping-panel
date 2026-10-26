<?php

namespace App\DTO\user;

final readonly class UserUpdateData
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phone = null,
        public ?string $recoveryEmail = null,
    ) {
    }

    /**
     * Return only fields supplied for this partial update.
     *
     * @return array<string, string>
     */
    public function toModelAttributes(): array
    {
        return array_filter([
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'phone' => $this->phone,
            'recovery_email' => $this->recoveryEmail,
        ], static fn (?string $value): bool => $value !== null);
    }
}
