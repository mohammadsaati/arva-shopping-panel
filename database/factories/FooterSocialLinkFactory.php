<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\FooterSocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FooterSocialLink> */
class FooterSocialLinkFactory extends Factory
{
    protected $model = FooterSocialLink::class;

    public function definition(): array
    {
        return [
            'label' => fake()->word(),
            'icon' => null,
            'image_path' => null,
            'url' => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 20),
            'status' => GlobalStatusEnum::ACTIVE,
        ];
    }
}
