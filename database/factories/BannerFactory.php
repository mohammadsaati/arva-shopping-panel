<?php

namespace Database\Factories;

use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Models\Banner;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Banner> */
class BannerFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'image' => $this->randomPublicImagePath(),
            'type' => BannerTypeEnum::LINK,
            'product_id' => null,
            'category_id' => null,
            'link' => fake()->url(),
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
        ];
    }
}
