<?php

namespace Database\Seeders;

use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Models\Discount;
use Illuminate\Database\Seeder;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $discounts = [
            [
                'name' => 'تخفیف خوش‌آمدگویی',
                'code' => 'WELCOME10',
                'user_id' => null,
                'type' => DiscountTypeEnum::PERCENTAGE,
                'amount' => 10,
                'min_order_amount' => 200_000,
                'max_discount_amount' => 500_000,
                'usage_limit' => 500,
                'usage_limit_per_user' => 1,
            ],
            [
                'name' => 'تخفیف خرید عمده',
                'code' => 'SAVE50000',
                'user_id' => null,
                'type' => DiscountTypeEnum::FIXED,
                'amount' => 50_000,
                'min_order_amount' => 500_000,
                'max_discount_amount' => null,
                'usage_limit' => 250,
                'usage_limit_per_user' => 2,
            ],
        ];

        foreach ($discounts as $attributes) {
            Discount::query()->firstOrCreate(
                ['code' => $attributes['code']],
                [
                    ...$attributes,
                    'status' => GlobalStatusEnum::ACTIVE,
                    'starts_at' => null,
                    'ends_at' => null,
                    'usage_count' => 0,
                ],
            );
        }
    }
}
