<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\FooterLink;
use App\Models\FooterLinkGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<FooterLink> */
class FooterLinkFactory extends Factory
{
    protected $model = FooterLink::class;

    public function definition(): array
    {
        return [
            'footer_link_group_id' => FooterLinkGroup::factory(),
            'label' => fake()->words(2, true),
            'url' => '/shop',
            'opens_in_new_tab' => false,
            'sort_order' => fake()->numberBetween(0, 20),
            'status' => GlobalStatusEnum::ACTIVE,
        ];
    }
}
