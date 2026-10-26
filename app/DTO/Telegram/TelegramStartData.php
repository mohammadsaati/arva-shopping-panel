<?php

namespace App\DTO\Telegram;

final readonly class TelegramStartData
{
    public function __construct(
        public string $chatId,
        public string $chatType,
        public string $text,
    ) {}
}
