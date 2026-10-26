<?php

namespace Tests\Unit\Payment;

use App\Enums\PaymentGatewayDriverEnum;
use App\Models\PaymentGateway;
use PHPUnit\Framework\TestCase;

class PaymentGatewayDisplayImageTest extends TestCase
{
    public function test_falls_back_to_driver_default_when_image_is_missing(): void
    {
        $gateway = new PaymentGateway();
        $gateway->driver = PaymentGatewayDriverEnum::ZARINPAL;
        $gateway->image = null;

        $this->assertSame('assets/media/payments/zarinpal.svg', $gateway->displayImage());
    }

    public function test_uses_uploaded_image_when_present(): void
    {
        $gateway = new PaymentGateway();
        $gateway->driver = PaymentGatewayDriverEnum::ZARINPAL;
        $gateway->image = 'payment-gateways/custom.png';

        $this->assertSame('payment-gateways/custom.png', $gateway->displayImage());
    }
}
