<?php

namespace App\Services\Contracts;

interface UserPasswordResetServiceInterface
{
    public function sendResetLink(string $phone): void;

    public function resetPassword(string $phone, string $token, string $password): void;
}
