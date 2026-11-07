<?php

namespace App\DTO\Commission;

final readonly class CommissionCreateData
{
    public function __construct(
        public string $title,
        public string $percentage,
    ) {
    }
}
