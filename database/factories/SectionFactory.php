<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\Section;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Section> */
class SectionFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = Section::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => fake()->unique()->slug(),
            'bg_color' => fake()->randomElement(['#EFF6FF', '#F8FAFC', '#FEF3C7', '#FCE7F3']),
            'bg_image' => $this->randomPublicImagePath(),
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
        ];
    }
}
