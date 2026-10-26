<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;
use App\Models\Slider;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Slider> */
class SliderFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = Slider::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'priority' => fake()->numberBetween(0, 100),
            'image' => $this->randomPublicImagePath(),
            'product_id' => null,
            'category_id' => null,
            'section_id' => null,
            'link' => fake()->url(),
            'type' => SliderTypeEnum::LINK,
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
        ];
    }
}
