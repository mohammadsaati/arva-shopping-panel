<?php

namespace App\DTO\Payment;

use App\Support\ZarinpalUserMessage;

final readonly class ZarinpalPaymentVerifyResult
{
    public function __construct(
        public bool $verified,
        public bool $alreadyVerified,
        public ?int $refId,
        public ?string $cardPan,
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
        $rawMessage = self::stringValue($data['message'] ?? $errors['message'] ?? null)
            ?? 'تأیید پرداخت زرین‌پال ناموفق بود.';
        $refId = self::intValue($data['ref_id'] ?? null);
        $cardPan = self::stringValue($data['card_pan'] ?? null);
        $verified = $httpStatus >= 200 && $httpStatus < 300 && ($code === 100 || $code === 101);

        $message = $verified
            ? $rawMessage
            : ZarinpalUserMessage::forVerify($code, $rawMessage);

        return new self(
            verified: $verified,
            alreadyVerified: $code === 101,
            refId: $refId,
            cardPan: $cardPan,
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
