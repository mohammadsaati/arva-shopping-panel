<?php

namespace App\Services\Contracts;

use App\DTO\Admin\AdminLoginData;

interface AdminAuthServiceInterface
{
    public function attempt(AdminLoginData $data): bool;

    public function logout(): void;
}
