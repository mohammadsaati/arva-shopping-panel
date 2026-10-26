<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\FooterAppLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FooterAppLink> */
class FooterAppLinkFactory extends Factory
{
    protected $model = FooterAppLink::class;

    public function definition(): array
    {
        return [
            'label' => fake()->word(),
            'image_path' => null,
            'url' => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 20),
            'status' => GlobalStatusEnum::ACTIVE,
        ];
    }
}
