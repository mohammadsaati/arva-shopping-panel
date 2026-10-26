<?php

namespace App\DTO\Discount;

use App\Models\Discount;

final readonly class DiscountQuoteData
{
    public function __construct(
        public Discount $discount,
        public int $discountAmount,
        public int $eligibleSubtotal,
    ) {
    }
}
