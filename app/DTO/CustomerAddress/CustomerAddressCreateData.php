<?php

namespace App\DTO\CustomerAddress;

final readonly class CustomerAddressCreateData
{
    public function __construct(
        public int $provinceId,
        public int $cityId,
        public string $fullAddress,
        public string $phoneNumber,
        public string $recipientName,
        public ?string $recipientNationalCode,
    ) {
    }
}
