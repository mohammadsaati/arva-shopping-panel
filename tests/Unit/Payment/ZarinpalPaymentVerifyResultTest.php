<?php

namespace Tests\Unit\Payment;

use App\DTO\Payment\ZarinpalPaymentVerifyResult;
use PHPUnit\Framework\TestCase;

class ZarinpalPaymentVerifyResultTest extends TestCase
{
    public function test_from_payload_marks_first_verify_as_paid(): void
    {
        $result = ZarinpalPaymentVerifyResult::fromPayload([
            'data' => [
                'code' => 100,
                'message' => 'Verified',
                'ref_id' => 201,
                'card_pan' => '502229******5995',
            ],
        ], 200);

        $this->assertTrue($result->verified);
        $this->assertFalse($result->alreadyVerified);
        $this->assertSame(201, $result->refId);
        $this->assertSame('502229******5995', $result->cardPan);
    }

    public function test_from_payload_treats_code_101_as_already_verified(): void
    {
        $result = ZarinpalPaymentVerifyResult::fromPayload([
            'data' => ['code' => 101, 'message' => 'Already verified', 'ref_id' => 201],
        ], 200);

        $this->assertTrue($result->verified);
        $this->assertTrue($result->alreadyVerified);
    }

    public function test_from_payload_rejects_failed_verify(): void
    {
        $result = ZarinpalPaymentVerifyResult::fromPayload([
            'errors' => ['code' => -51, 'message' => 'Failed'],
        ], 406);

        $this->assertFalse($result->verified);
        $this->assertFalse($result->alreadyVerified);
        $this->assertSame(-51, $result->code);
        $this->assertSame('پرداخت انجام نشده است.', $result->message);
    }
}
