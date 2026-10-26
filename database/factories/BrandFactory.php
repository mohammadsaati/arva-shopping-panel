<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\Brand;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = Brand::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(),
            'image' => $this->randomPublicImagePath(),
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
        ];
    }
}
