<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\StorefrontPasswordResetNotification;
use App\Services\Contracts\UserPasswordResetServiceInterface;
use Illuminate\Validation\ValidationException;

class UserPasswordResetService implements UserPasswordResetServiceInterface
{
    public function sendResetLink(string $phone): void
    {
        $user = User::findActiveCustomerByPhone($phone);

        if ($user === null) {
            return;
        }

        if ($user->recovery_email === null || trim($user->recovery_email) === '') {
            throw ValidationException::withMessages([
                'phone' => 'برای این شماره ایمیل بازیابی ثبت نشده است. از حساب کاربری ایمیل بازیابی را اضافه کنید یا با پشتیبانی تماس بگیرید.',
            ]);
        }

        $token = $user->issuePasswordResetToken();

        $user->notify(new StorefrontPasswordResetNotification($token));
    }

    public function resetPassword(string $phone, string $token, string $password): void
    {
        User::resetPasswordFromToken($phone, $token, $password);
    }
}
