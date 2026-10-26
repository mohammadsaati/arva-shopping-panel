<?php

namespace App\DTO\Telegram;

final readonly class TelegramWebhookRegistrationResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?int $errorCode = null,
    ) {}

    /** @return array{success: bool, message: string, errorCode: int|null} */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'errorCode' => $this->errorCode,
        ];
    }
}
