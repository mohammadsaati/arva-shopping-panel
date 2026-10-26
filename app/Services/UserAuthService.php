<?php

namespace App\Services;

use App\DTO\user\UserCreateData;
use App\Models\User;
use App\Services\Contracts\UserAuthServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthService implements UserAuthServiceInterface
{
    public function __construct(private readonly UserServiceInterface $users) {}

    public function attempt(string $phone, string $password): bool
    {
        $user = User::findActiveCustomerByPhone($phone);

        if ($user === null || ! Hash::check($password, $user->password)) {
            return false;
        }

        Auth::guard('web')->login($user);

        return true;
    }

    public function register(UserCreateData $data): User
    {
        $user = $this->users->create($data);
        Auth::guard('web')->login($user);

        return $user;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
    }
}
