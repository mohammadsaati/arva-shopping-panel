<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Product;
use App\Models\ProductCity;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductCity> */
class ProductCityFactory extends Factory
{
    protected $model = ProductCity::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'city_id' => City::factory(),
            'shipping_price' => fake()->numberBetween(0, 500000),
        ];
    }
}
