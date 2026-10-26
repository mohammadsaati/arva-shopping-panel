<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\FooterFeature;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FooterFeature> */
class FooterFeatureFactory extends Factory
{
    protected $model = FooterFeature::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'icon' => 'ki-filled ki-verify',
            'image_path' => null,
            'url' => '/shop',
            'sort_order' => fake()->numberBetween(0, 20),
            'status' => GlobalStatusEnum::ACTIVE,
        ];
    }
}
