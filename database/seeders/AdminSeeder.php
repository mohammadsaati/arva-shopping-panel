<?php

namespace Database\Seeders;

use App\Enums\GlobalStatusEnum;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.seed.email');
        $password = config('admin.seed.password');
        $name = config('admin.seed.name', 'Test Admin');

        if (! is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new RuntimeException('Set a valid ADMIN_EMAIL in the environment before seeding the admin account.');
        }

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Set ADMIN_PASSWORD in the environment before seeding the admin account.');
        }

        Admin::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => is_string($name) && $name !== '' ? $name : 'Test Admin',
                'password' => $password,
                'status' => GlobalStatusEnum::ACTIVE->value,
            ],
        );
    }
}
