<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        $name = fake()->city();

        return [
            'province_id' => Province::query()->inRandomOrder()->value('id') ?? Province::factory(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(10)),
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
        ];
    }
}
