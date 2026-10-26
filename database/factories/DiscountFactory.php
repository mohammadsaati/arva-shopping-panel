<?php

namespace Database\Factories;

use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Discount>
 */
class DiscountFactory extends Factory
{
    protected $model = Discount::class;

    public function definition(): array
    {
        $type = fake()->randomElement(DiscountTypeEnum::cases());

        return [
            'user_id' => null,
            'name' => fake()->words(3, true),
            'code' => strtoupper(fake()->unique()->bothify('SAVE-####')),
            'type' => $type,
            'amount' => $type === DiscountTypeEnum::PERCENTAGE
                ? fake()->numberBetween(5, 30)
                : fake()->numberBetween(10_000, 200_000),
            'min_order_amount' => fake()->optional()->numberBetween(100_000, 1_000_000),
            'max_discount_amount' => $type === DiscountTypeEnum::PERCENTAGE
                ? fake()->optional()->numberBetween(100_000, 1_000_000)
                : null,
            'starts_at' => null,
            'ends_at' => null,
            'usage_limit' => fake()->optional()->numberBetween(50, 500),
            'usage_limit_per_user' => fake()->optional()->numberBetween(1, 3),
            'usage_count' => 0,
            'status' => GlobalStatusEnum::ACTIVE,
        ];
    }
}
