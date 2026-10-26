<?php

namespace App\Services\Contracts;

use App\DTO\Payment\ZarinpalPaymentRequestResult;
use App\DTO\Payment\ZarinpalPaymentVerifyResult;

interface ZarinpalClientInterface
{
    /**
     * @param array{mobile?: string, email?: string, order_id?: string} $metadata
     */
    public function requestPayment(
        string $merchantId,
        int $amount,
        string $callbackUrl,
        string $description,
        string $currency,
        bool $sandbox,
        array $metadata = [],
    ): ZarinpalPaymentRequestResult;

    public function verifyPayment(
        string $merchantId,
        int $amount,
        string $authority,
        bool $sandbox,
    ): ZarinpalPaymentVerifyResult;

    public function startPayUrl(string $authority, bool $sandbox): string;
}
