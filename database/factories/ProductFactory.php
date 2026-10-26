<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\Color;
use App\Models\Item;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'price' => fake()->numberBetween(100000, 50000000),
            'color_id' => fake()->boolean(80) ? Color::factory() : null,
            'size_id' => fake()->boolean(80) ? Size::factory() : null,
            'stock' => fake()->numberBetween(0, 200),
            'shipping_price' => fake()->numberBetween(0, 500000),
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
        ];
    }
}
