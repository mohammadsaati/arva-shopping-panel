<?php

namespace App\DTO\Commission;

final readonly class CommissionUpdateData
{
    public function __construct(
        public ?string $title = null,
        public ?string $percentage = null,
    ) {
    }
}
