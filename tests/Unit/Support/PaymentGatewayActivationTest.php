<?php

namespace Tests\Unit\Support;

use App\DTO\Payment\PaymentGatewaySettings;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentGatewayDriverEnum;
use App\Support\PaymentGatewayActivation;
use PHPUnit\Framework\TestCase;

class PaymentGatewayActivationTest extends TestCase
{
    public function test_is_valid_merchant_id_accepts_uuid(): void
    {
        $this->assertTrue(PaymentGatewayActivation::isValidMerchantId('1344b5d4-0048-11e8-94db-005056a205be'));
    }

    public function test_is_valid_merchant_id_rejects_empty_and_short_values(): void
    {
        $this->assertFalse(PaymentGatewayActivation::isValidMerchantId(''));
        $this->assertFalse(PaymentGatewayActivation::isValidMerchantId('not-a-merchant'));
        $this->assertFalse(PaymentGatewayActivation::isValidMerchantId('1344b5d4-0048-11e8-94db'));
    }

    public function test_inactive_gateway_is_ready_without_merchant(): void
    {
        $this->assertTrue(PaymentGatewayActivation::isReady(
            PaymentGatewayDriverEnum::ZARINPAL,
            GlobalStatusEnum::INACTIVE,
            PaymentGatewaySettings::empty(),
        ));
    }

    public function test_active_zarinpal_without_merchant_is_not_ready(): void
    {
        $this->assertFalse(PaymentGatewayActivation::isReady(
            PaymentGatewayDriverEnum::ZARINPAL,
            GlobalStatusEnum::ACTIVE,
            PaymentGatewaySettings::empty(),
        ));
    }

    public function test_active_zarinpal_with_valid_merchant_is_ready(): void
    {
        $this->assertTrue(PaymentGatewayActivation::isReady(
            PaymentGatewayDriverEnum::ZARINPAL,
            GlobalStatusEnum::ACTIVE,
            new PaymentGatewaySettings(merchantId: '1344b5d4-0048-11e8-94db-005056a205be'),
        ));
    }
}
