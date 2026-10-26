<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = Item::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $isAmazing = fake()->boolean(10);

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(),
            'code' => fake()->unique()->numberBetween(100000, 999999999),
            'image' => $this->randomPublicImagePath(),
            'description' => fake()->optional()->paragraphs(2, true),
            'category_id' => Category::factory(),
            'brand_id' => fake()->boolean(75) ? Brand::factory() : null,
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
            'is_amazing' => $isAmazing,
            'amazing_end_time' => $isAmazing ? fake()->dateTimeBetween('now', '+30 days') : null,
        ];
    }
}
