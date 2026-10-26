<?php

namespace App\DTO\Discount;

final readonly class DiscountQuoteRequestData
{
    public function __construct(
        public string $code,
    ) {
    }
}
