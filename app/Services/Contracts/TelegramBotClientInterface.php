<?php

namespace App\Services\Contracts;

use App\DTO\Telegram\TelegramWebhookRegistrationResult;

interface TelegramBotClientInterface
{
    public function sendText(string $chatId, string $text): bool;

    public function registerWebhook(string $url, string $secret): TelegramWebhookRegistrationResult;
}
