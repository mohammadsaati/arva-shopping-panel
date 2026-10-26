<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\SectionImage;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SectionImage> */
class SectionImageFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = SectionImage::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'image' => $this->randomPublicImagePath(),
        ];
    }
}
