<?php

namespace App\DTO\Province;

use App\Enums\GlobalStatusEnum;

final readonly class ProvinceCreateData
{
    public function __construct(
        public string $name,
        public GlobalStatusEnum $status,
    ) {
    }
}
