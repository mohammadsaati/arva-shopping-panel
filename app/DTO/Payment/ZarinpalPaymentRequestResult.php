<?php

namespace App\DTO\Payment;

use App\Support\ZarinpalUserMessage;

final readonly class ZarinpalPaymentRequestResult
{
    public function __construct(
        public bool $ok,
        public ?string $authority,
        public ?int $code,
        public string $message,
    ) {
    }

    /** @param array<string, mixed>|null $payload */
    public static function fromPayload(?array $payload, int $httpStatus): self
    {
        $data = is_array($payload['data'] ?? null) ? $payload['data'] : [];
        $errors = is_array($payload['errors'] ?? null) ? $payload['errors'] : [];
        $code = self::intValue($data['code'] ?? $errors['code'] ?? null);
        $authority = isset($data['authority']) && is_string($data['authority']) && $data['authority'] !== ''
            ? $data['authority']
            : null;
        $rawMessage = self::stringValue($data['message'] ?? $errors['message'] ?? null)
            ?? 'پاسخ زرین‌پال قابل خواندن نبود.';

        $ok = $httpStatus >= 200 && $httpStatus < 300 && $code === 100 && $authority !== null;

        $message = $ok
            ? $rawMessage
            : ZarinpalUserMessage::forRequest($code, $rawMessage);

        return new self(
            ok: $ok,
            authority: $authority,
            code: $code,
            message: $message,
        );
    }

    private static function intValue(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && is_numeric($value)) {
            return (int) $value;
        }

        return null;
    }

    private static function stringValue(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
