<?php

namespace Tests\Unit\Enums;

use App\Enums\OrderStatusEnum;
use PHPUnit\Framework\TestCase;

class OrderStatusEnumRequiresPaidPaymentTest extends TestCase
{
    public function test_fulfillment_statuses_require_paid_payment(): void
    {
        $this->assertTrue(OrderStatusEnum::PROCESSING->requiresPaidPayment());
        $this->assertTrue(OrderStatusEnum::SHIPPED->requiresPaidPayment());
        $this->assertTrue(OrderStatusEnum::DELIVERED->requiresPaidPayment());
    }

    public function test_non_fulfillment_statuses_do_not_require_paid_payment(): void
    {
        $this->assertFalse(OrderStatusEnum::PENDING->requiresPaidPayment());
        $this->assertFalse(OrderStatusEnum::ON_HOLD->requiresPaidPayment());
        $this->assertFalse(OrderStatusEnum::CANCELLED->requiresPaidPayment());
    }
}
