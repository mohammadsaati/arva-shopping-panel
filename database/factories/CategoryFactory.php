<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\Category;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'parent_id' => null,
            'slug' => fake()->unique()->slug(),
            'priority' => fake()->numberBetween(0, 100),
            'icon' => fake()->optional()->randomElement(['ki-filled ki-category', 'ki-filled ki-tag', 'ki-filled ki-shop']),
            'image' => $this->randomPublicImagePath(),
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
        ];
    }
}
