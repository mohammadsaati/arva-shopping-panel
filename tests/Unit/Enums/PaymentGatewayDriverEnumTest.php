<?php

namespace Tests\Unit\Enums;

use App\Enums\PaymentGatewayDriverEnum;
use PHPUnit\Framework\TestCase;

class PaymentGatewayDriverEnumTest extends TestCase
{
    public function test_zarinpal_has_a_default_image_path(): void
    {
        $this->assertSame(
            'assets/media/payments/zarinpal.svg',
            PaymentGatewayDriverEnum::ZARINPAL->defaultImage(),
        );
    }
}
