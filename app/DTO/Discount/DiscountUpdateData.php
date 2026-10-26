<?php

namespace App\DTO\Discount;

use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;

final readonly class DiscountUpdateData
{
    /** @param array<int, int> $productIds @param array<int, int> $categoryIds */
    public function __construct(
        public ?string $name = null,
        public ?string $code = null,
        public ?int $userId = null,
        public bool $userIdProvided = false,
        public ?DiscountTypeEnum $type = null,
        public ?int $amount = null,
        public ?int $minOrderAmount = null,
        public bool $minOrderAmountProvided = false,
        public ?int $maxDiscountAmount = null,
        public bool $maxDiscountAmountProvided = false,
        public ?Carbon $startsAt = null,
        public bool $startsAtProvided = false,
        public ?Carbon $endsAt = null,
        public bool $endsAtProvided = false,
        public ?int $usageLimit = null,
        public bool $usageLimitProvided = false,
        public ?int $usageLimitPerUser = null,
        public bool $usageLimitPerUserProvided = false,
        public ?GlobalStatusEnum $status = null,
        public array $productIds = [],
        public bool $productIdsProvided = false,
        public array $categoryIds = [],
        public bool $categoryIdsProvided = false,
    ) {
    }

    /** @return array<string, string|int|Carbon|DiscountTypeEnum|GlobalStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'amount' => $this->amount,
            'status' => $this->status,
        ], static fn (string|int|DiscountTypeEnum|GlobalStatusEnum|null $value): bool => $value !== null);

        foreach ([
            'user_id' => [$this->userIdProvided, $this->userId],
            'min_order_amount' => [$this->minOrderAmountProvided, $this->minOrderAmount],
            'max_discount_amount' => [$this->maxDiscountAmountProvided, $this->maxDiscountAmount],
            'starts_at' => [$this->startsAtProvided, $this->startsAt],
            'ends_at' => [$this->endsAtProvided, $this->endsAt],
            'usage_limit' => [$this->usageLimitProvided, $this->usageLimit],
            'usage_limit_per_user' => [$this->usageLimitPerUserProvided, $this->usageLimitPerUser],
        ] as $attribute => [$provided, $value]) {
            if ($provided) {
                $attributes[$attribute] = $value;
            }
        }

        return $attributes;
    }
}
