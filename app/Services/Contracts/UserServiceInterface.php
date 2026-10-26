<?php

namespace App\Services\Contracts;

use App\DTO\user\UserCreateData;
use App\DTO\user\UserFilterData;
use App\DTO\user\UserPasswordChangeData;
use App\DTO\user\UserProfileUpdateData;
use App\DTO\user\UserUpdateData;
use App\DTO\user\UserAdminUpdateData;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

interface UserServiceInterface
{
    public function create(UserCreateData $data): User;

    public function details(int $userId): User;

    public function updateAdminDetails(User $user, UserAdminUpdateData $data): User;

    public function filter(UserFilterData $filters): Paginator;

    public function profile(User $user): User;

    public function updateProfile(User $user, UserUpdateData $data, ?UserProfileUpdateData $profile = null): User;

    public function changePassword(User $user, UserPasswordChangeData $data): void;
}
