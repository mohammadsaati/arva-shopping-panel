<?php

namespace App\DTO\CustomerAddress;

final readonly class CustomerAddressUpdateData
{
    public function __construct(
        public ?int $provinceId = null,
        public bool $provinceIdProvided = false,
        public ?int $cityId = null,
        public bool $cityIdProvided = false,
        public ?string $fullAddress = null,
        public bool $fullAddressProvided = false,
        public ?string $phoneNumber = null,
        public bool $phoneNumberProvided = false,
        public ?string $recipientName = null,
        public bool $recipientNameProvided = false,
        public ?string $recipientNationalCode = null,
        public bool $recipientNationalCodeProvided = false,
    ) {
    }

    /** @return array<string, int|string|null> */
    public function toModelAttributes(): array
    {
        $attributes = [];

        if ($this->provinceIdProvided && $this->provinceId !== null) {
            $attributes['province_id'] = $this->provinceId;
        }

        if ($this->cityIdProvided && $this->cityId !== null) {
            $attributes['city_id'] = $this->cityId;
        }

        if ($this->fullAddressProvided && $this->fullAddress !== null) {
            $attributes['full_address'] = $this->fullAddress;
        }

        if ($this->phoneNumberProvided && $this->phoneNumber !== null) {
            $attributes['phone_number'] = $this->phoneNumber;
        }

        if ($this->recipientNameProvided && $this->recipientName !== null) {
            $attributes['recipient_name'] = $this->recipientName;
        }

        if ($this->recipientNationalCodeProvided) {
            $attributes['recipient_national_code'] = $this->recipientNationalCode;
        }

        return $attributes;
    }
}
