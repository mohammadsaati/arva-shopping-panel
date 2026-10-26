<?php

namespace App\DTO\user;

final readonly class UserProfileUpdateData
{
    public function __construct(
        public ?string $bankCard = null,
        public bool $bankCardProvided = false,
        public ?string $job = null,
        public bool $jobProvided = false,
        public ?string $cityId = null,
        public bool $cityIdProvided = false,
    ) {
    }

    /** @return array<string, string|null> */
    public function toModelAttributes(): array
    {
        $attributes = [];

        if ($this->bankCardProvided) {
            $attributes['bank_card'] = $this->bankCard;
        }

        if ($this->jobProvided) {
            $attributes['job'] = $this->job;
        }

        if ($this->cityIdProvided) {
            $attributes['city_id'] = $this->cityId;
        }

        return $attributes;
    }
}
