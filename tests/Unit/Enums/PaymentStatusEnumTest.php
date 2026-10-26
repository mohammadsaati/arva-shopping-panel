<?php

namespace Tests\Unit\Enums;

use App\Enums\PaymentStatusEnum;
use PHPUnit\Framework\TestCase;

class PaymentStatusEnumTest extends TestCase
{
    public function test_is_paid_only_for_paid_case(): void
    {
        $this->assertTrue(PaymentStatusEnum::PAID->isPaid());
        $this->assertFalse(PaymentStatusEnum::PENDING->isPaid());
        $this->assertFalse(PaymentStatusEnum::FAILED->isPaid());
    }

    public function test_can_retry_for_pending_and_failed(): void
    {
        $this->assertTrue(PaymentStatusEnum::PENDING->canRetry());
        $this->assertTrue(PaymentStatusEnum::FAILED->canRetry());
        $this->assertFalse(PaymentStatusEnum::PAID->canRetry());
    }

    public function test_color_matches_status(): void
    {
        $this->assertSame('warning', PaymentStatusEnum::PENDING->color());
        $this->assertSame('success', PaymentStatusEnum::PAID->color());
        $this->assertSame('danger', PaymentStatusEnum::FAILED->color());
    }
}
