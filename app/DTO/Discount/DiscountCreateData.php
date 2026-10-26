<?php

namespace App\DTO\Discount;

use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;

final readonly class DiscountCreateData
{
    /** @param array<int, int> $productIds @param array<int, int> $categoryIds */
    public function __construct(
        public string $name,
        public string $code,
        public ?int $userId,
        public DiscountTypeEnum $type,
        public int $amount,
        public ?int $minOrderAmount,
        public ?int $maxDiscountAmount,
        public ?Carbon $startsAt,
        public ?Carbon $endsAt,
        public ?int $usageLimit,
        public ?int $usageLimitPerUser,
        public GlobalStatusEnum $status,
        public array $productIds = [],
        public array $categoryIds = [],
    ) {
    }
}
