<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bank_card' => fake()->optional()->numerify('################'),
            'job' => fake()->optional()->jobTitle(),
            'city_id' => fake()->optional()->numerify('##'),
        ];
    }
}
