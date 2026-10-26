<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\FooterLinkGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FooterLinkGroup> */
class FooterLinkGroupFactory extends Factory
{
    protected $model = FooterLinkGroup::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(2, true),
            'sort_order' => fake()->numberBetween(0, 20),
            'status' => GlobalStatusEnum::ACTIVE,
        ];
    }
}
