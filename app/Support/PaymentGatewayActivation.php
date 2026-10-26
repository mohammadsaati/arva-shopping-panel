<?php

namespace App\Support;

use App\DTO\Payment\PaymentGatewaySettings;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentGatewayDriverEnum;
use Illuminate\Validation\ValidationException;

final class PaymentGatewayActivation
{
    public static function isValidMerchantId(string $merchantId): bool
    {
        return (bool) preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $merchantId,
        );
    }

    public static function isReady(
        PaymentGatewayDriverEnum $driver,
        GlobalStatusEnum $status,
        PaymentGatewaySettings $settings,
    ): bool {
        if ($status !== GlobalStatusEnum::ACTIVE || $driver !== PaymentGatewayDriverEnum::ZARINPAL) {
            return true;
        }

        return self::isValidMerchantId($settings->merchantId);
    }

    public static function assertReady(
        PaymentGatewayDriverEnum $driver,
        GlobalStatusEnum $status,
        PaymentGatewaySettings $settings,
    ): void {
        if (self::isReady($driver, $status, $settings)) {
            return;
        }

        throw ValidationException::withMessages([
            'merchant_id' => 'برای فعال‌کردن زرین‌پال باید کد پذیرنده ۳۶ کاراکتری معتبر را وارد کنید.',
            'status' => 'تا وقتی کد پذیرنده ثبت نشده، این درگاه را فعال نکنید.',
        ]);
    }
}
