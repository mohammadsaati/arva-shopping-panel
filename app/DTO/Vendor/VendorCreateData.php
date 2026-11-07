<?php

namespace App\DTO\Vendor;

final readonly class VendorCreateData
{
    public function __construct(
        public string $companyName,
        public int $cityId,
        public ?int $commissionId,
    ) {
    }
}
