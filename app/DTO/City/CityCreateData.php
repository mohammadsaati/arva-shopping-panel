<?php

namespace App\DTO\City;

use App\Enums\GlobalStatusEnum;

final readonly class CityCreateData
{
    public function __construct(
        public int $provinceId,
        public string $name,
        public string $slug,
        public GlobalStatusEnum $status,
    ) {
    }
}
