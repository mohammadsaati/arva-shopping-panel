<?php

namespace App\Services\Contracts;

use App\DTO\user\UserCreateData;
use App\Models\User;

interface UserAuthServiceInterface
{
    public function attempt(string $phone, string $password): bool;

    public function register(UserCreateData $data): User;

    public function logout(): void;
}
