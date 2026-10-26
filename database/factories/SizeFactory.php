<?php

namespace Database\Factories;

use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Size>
 */
class SizeFactory extends Factory
{
    public const VALUES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Free Size'];

    protected $model = Size::class;

    public function definition(): array
    {
        return [
            'value' => fake()->unique()->randomElement(self::VALUES),
        ];
    }
}
