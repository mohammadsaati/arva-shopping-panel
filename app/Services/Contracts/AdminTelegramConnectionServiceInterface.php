<?php

namespace App\Services\Contracts;

use App\DTO\Telegram\TelegramStartData;
use App\DTO\Telegram\TelegramWebhookRegistrationResult;
use App\Models\Admin;
use Carbon\Carbon;

interface AdminTelegramConnectionServiceInterface
{
    /** @return array{configured: bool, connected: bool, connectedAt: Carbon|null, botUsername: string|null, webhookUrl: string|null} */
    public function overview(Admin $admin): array;

    public function registerWebhook(): TelegramWebhookRegistrationResult;

    public function createLink(Admin $admin): ?string;

    public function completeFromStart(TelegramStartData $data): void;

    public function sendTest(Admin $admin): bool;

    public function disconnect(Admin $admin): void;
}
