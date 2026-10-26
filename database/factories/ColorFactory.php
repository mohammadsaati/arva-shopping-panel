<?php

namespace Database\Factories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Color>
 */
class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition(): array
    {
        return [
            'name' => ucwords(fake()->unique()->words(2, true)) . ' Color',
            'value' => fake()->hexColor(),
        ];
    }
}
