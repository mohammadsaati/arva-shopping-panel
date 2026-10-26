<?php

namespace App\DTO\Payment;

use App\Enums\PaymentCurrencyEnum;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final readonly class PaymentGatewaySettings implements Castable
{
    public function __construct(
        public string $merchantId = '',
        public bool $sandbox = false,
        public PaymentCurrencyEnum $currency = PaymentCurrencyEnum::IRT,
    ) {
    }

    public static function empty(): self
    {
        return new self();
    }

    public static function from(mixed $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        if (! is_array($value)) {
            return self::empty();
        }

        $merchantId = $value['merchant_id'] ?? $value['merchantId'] ?? '';
        $currency = $value['currency'] ?? null;

        return new self(
            merchantId: is_string($merchantId) ? trim($merchantId) : '',
            sandbox: (bool) ($value['sandbox'] ?? false),
            currency: $currency instanceof PaymentCurrencyEnum
                ? $currency
                : (PaymentCurrencyEnum::tryFrom((string) $currency) ?? PaymentCurrencyEnum::IRT),
        );
    }

    /** @return array{merchant_id: string, sandbox: bool, currency: string} */
    public function toArray(): array
    {
        return [
            'merchant_id' => $this->merchantId,
            'sandbox' => $this->sandbox,
            'currency' => $this->currency->value,
        ];
    }

    public function merge(
        ?string $merchantId = null,
        bool $merchantIdProvided = false,
        ?bool $sandbox = null,
        bool $sandboxProvided = false,
        ?PaymentCurrencyEnum $currency = null,
        bool $currencyProvided = false,
    ): self {
        return new self(
            merchantId: $merchantIdProvided ? trim((string) $merchantId) : $this->merchantId,
            sandbox: $sandboxProvided && $sandbox !== null ? $sandbox : $this->sandbox,
            currency: $currencyProvided && $currency !== null ? $currency : $this->currency,
        );
    }

    public static function castUsing(array $arguments): CastsAttributes
    {
        return new class implements CastsAttributes
        {
            public function get(Model $model, string $key, mixed $value, array $attributes): PaymentGatewaySettings
            {
                return PaymentGatewaySettings::from($value);
            }

            /** @return array<string, string> */
            public function set(Model $model, string $key, mixed $value, array $attributes): array
            {
                return [
                    $key => json_encode(PaymentGatewaySettings::from($value)->toArray(), JSON_UNESCAPED_UNICODE),
                ];
            }
        };
    }
}
