<?php

namespace Tests\Unit\Enums;

use App\Enums\TransactionReasonTypeEnum;
use PHPUnit\Framework\TestCase;

class TransactionReasonTypeEnumTest extends TestCase
{
    public function test_reason_types_require_reason_id(): void
    {
        $this->assertTrue(TransactionReasonTypeEnum::ORDER->requiresReasonId());
        $this->assertTrue(TransactionReasonTypeEnum::WALLET->requiresReasonId());
    }

    public function test_backing_values_are_stable(): void
    {
        $this->assertSame('ORDER', TransactionReasonTypeEnum::ORDER->value);
        $this->assertSame('WALLET', TransactionReasonTypeEnum::WALLET->value);
    }
}
