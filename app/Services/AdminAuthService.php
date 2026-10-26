<?php

namespace App\Services;

use App\DTO\Admin\AdminLoginData;
use App\Models\Admin;
use App\Services\Contracts\AdminAuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthService implements AdminAuthServiceInterface
{
    public function attempt(AdminLoginData $data): bool
    {
        $admin = Admin::findActiveByEmail($data->email);

        if ($admin === null || ! Hash::check($data->password, $admin->password)) {
            return false;
        }

        Auth::guard('admin')->login($admin);

        return true;
    }

    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }
}
