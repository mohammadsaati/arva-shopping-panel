<?php

namespace Database\Seeders;

use App\Enums\GlobalStatusEnum;
use App\Models\City;
use App\Models\Province;
use Database\Factories\ProvinceFactory;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $provinces = collect(ProvinceFactory::NAMES)
            ->map(function (string $name): Province {
                $province = Province::query()->where('name', $name)->first();

                if ($province !== null) {
                    return $province;
                }

                return Province::factory()
                    ->state([
                        'name' => $name,
                        'status' => GlobalStatusEnum::ACTIVE,
                    ])
                    ->create();
            });

        $missingCitiesCount = max(0, 100 - City::query()->count());

        if ($missingCitiesCount === 0) {
            return;
        }

        $provinceIds = $provinces->pluck('id')->all();

        City::factory()
            ->count($missingCitiesCount)
            ->state(fn (): array => ['province_id' => fake()->randomElement($provinceIds)])
            ->create();
    }
}
