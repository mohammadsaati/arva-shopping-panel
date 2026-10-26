<?php

namespace App\DTO\Payment;

use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use Illuminate\Http\UploadedFile;

final readonly class PaymentGatewayUpdateData
{
    public function __construct(
        public ?string $name = null,
        public ?GlobalStatusEnum $status = null,
        public ?string $merchantId = null,
        public bool $merchantIdProvided = false,
        public ?bool $sandbox = null,
        public bool $sandboxProvided = false,
        public ?PaymentCurrencyEnum $currency = null,
        public bool $currencyProvided = false,
        public ?UploadedFile $image = null,
        public bool $removeImage = false,
    ) {
    }

    /** @return array<string, mixed> */
    public function toModelAttributes(): array
    {
        return array_filter([
            'name' => $this->name,
            'status' => $this->status,
        ], static fn (string|GlobalStatusEnum|null $value): bool => $value !== null);
    }

    public function appliedSettings(?PaymentGatewaySettings $current = null): PaymentGatewaySettings
    {
        return ($current ?? PaymentGatewaySettings::empty())->merge(
            merchantId: $this->merchantId,
            merchantIdProvided: $this->merchantIdProvided,
            sandbox: $this->sandbox,
            sandboxProvided: $this->sandboxProvided,
            currency: $this->currency,
            currencyProvided: $this->currencyProvided,
        );
    }
}
