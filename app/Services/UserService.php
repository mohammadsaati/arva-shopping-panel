<?php

namespace App\Services;

use App\DTO\user\UserCreateData;
use App\DTO\user\UserFilterData;
use App\DTO\user\UserPasswordChangeData;
use App\DTO\user\UserProfileUpdateData;
use App\DTO\user\UserUpdateData;
use App\DTO\user\UserAdminUpdateData;
use App\Models\User;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\VendorServiceInterface;
use App\Services\Contracts\CommissionServiceInterface;
use App\Enums\UserTypeEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\Paginator;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly VendorServiceInterface $vendors,
        private readonly CommissionServiceInterface $commissions,
    )
    {
    }

    public function create(UserCreateData $data): User
    {
        if (User::phoneExists($data->phone)) {
            throw ValidationException::withMessages([
                'phone' => 'این شماره موبایل قبلاً ثبت شده است.',
            ]);
        }

        if ($data->type === UserTypeEnum::VENDOR && $data->vendor === null) {
            throw ValidationException::withMessages(['vendor_company_name' => 'اطلاعات فروشنده را وارد کنید.']);
        }

        return User::runInTransaction(function () use ($data): User {
            $user = User::create($data, User::generateUniqueApiKey());

            if ($data->type === UserTypeEnum::VENDOR) {
                $this->vendors->createForUser($user, $data->vendor);
            }

            return $user;
        });
    }

    public function vendorCityOptions(): array
    {
        return $this->vendors->cityOptions();
    }

    public function vendorCommissionOptions(): array
    {
        return $this->commissions->selectionOptions();
    }

    public function details(int $userId): User
    {
        return User::details($userId);
    }

    public function updateAdminDetails(User $user, UserAdminUpdateData $data): User
    {
        if ($data->phone !== null && $data->phone !== $user->phone && $user->isPhoneTaken($data->phone)) {
            throw ValidationException::withMessages([
                'phone' => 'این شماره موبایل قبلاً ثبت شده است.',
            ]);
        }

        if ($user->vendor !== null && $data->type !== null && $data->type !== UserTypeEnum::VENDOR) {
            throw ValidationException::withMessages(['type' => 'حساب دارای فروشنده را نمی‌توان به نوع دیگری تغییر داد.']);
        }

        if ($data->type === UserTypeEnum::VENDOR && $data->vendor === null) {
            throw ValidationException::withMessages(['vendor_company_name' => 'اطلاعات فروشنده را وارد کنید.']);
        }

        return User::runInTransaction(function () use ($user, $data): User {
            $user->updateAdminDetails($data);

            if ($data->type === UserTypeEnum::VENDOR) {
                if ($user->vendor !== null) {
                    $this->vendors->updateForUser($user->vendor, $data->vendor);
                } else {
                    $this->vendors->createForUser($user, $data->vendor);
                }
            }

            return User::details($user->id);
        });
    }

    public function filter(UserFilterData $filters): Paginator
    {
        return User::filter($filters);
    }

    public function profile(User $user): User
    {
        return User::details($user->id);
    }

    public function updateProfile(User $user, UserUpdateData $data, ?UserProfileUpdateData $profile = null): User
    {
        if ($data->phone !== null && $data->phone !== $user->phone && $user->isPhoneTaken($data->phone)) {
            throw ValidationException::withMessages([
                'phone' => 'این شماره موبایل قبلاً ثبت شده است.',
            ]);
        }

        if ($data->recoveryEmail !== null && $data->recoveryEmail !== $user->recovery_email && User::recoveryEmailTaken($data->recoveryEmail, $user->id)) {
            throw ValidationException::withMessages([
                'recovery_email' => 'این ایمیل بازیابی قبلاً ثبت شده است.',
            ]);
        }

        return $user->updateProfile($data, $profile);
    }

    public function changePassword(User $user, UserPasswordChangeData $data): void
    {
        if (! Hash::check($data->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'رمز عبور فعلی درست نیست.',
            ]);
        }

        $user->updatePassword($data->newPassword);
    }
}
