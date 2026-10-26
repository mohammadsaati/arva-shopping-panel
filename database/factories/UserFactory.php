<?php

namespace Database\Factories;

use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'phone' => '09'.fake()->unique()->numerify('#########'),
            'password' => static::$password ??= Hash::make('password'),
            'api_key' => User::generateUniqueApiKey(),
            'type' => fake()->randomElement(UserTypeEnum::cases()),
            'status' => fake()->randomElement(GlobalStatusEnum::cases()),
            'remember_token' => Str::random(10),
        ];
    }
}
