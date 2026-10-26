<?php

namespace Tests\Unit\Payment;

use App\DTO\Payment\ZarinpalPaymentRequestResult;
use PHPUnit\Framework\TestCase;

class ZarinpalPaymentRequestResultTest extends TestCase
{
    public function test_from_payload_accepts_successful_request(): void
    {
        $result = ZarinpalPaymentRequestResult::fromPayload([
            'data' => [
                'code' => 100,
                'message' => 'Success',
                'authority' => 'A0000000000000000000000000000wwOGYpd',
            ],
            'errors' => [],
        ], 200);

        $this->assertTrue($result->ok);
        $this->assertSame('A0000000000000000000000000000wwOGYpd', $result->authority);
        $this->assertSame(100, $result->code);
        $this->assertSame('Success', $result->message);
    }

    public function test_from_payload_rejects_missing_authority(): void
    {
        $result = ZarinpalPaymentRequestResult::fromPayload([
            'data' => ['code' => 100, 'message' => 'Success'],
        ], 200);

        $this->assertFalse($result->ok);
        $this->assertNull($result->authority);
    }

    public function test_from_payload_reads_error_message(): void
    {
        $result = ZarinpalPaymentRequestResult::fromPayload([
            'data' => [],
            'errors' => ['code' => -9, 'message' => 'Invalid merchant'],
        ], 400);

        $this->assertFalse($result->ok);
        $this->assertSame(-9, $result->code);
        $this->assertSame('اطلاعات ارسالی به درگاه ناقص یا نامعتبر است.', $result->message);
    }

    public function test_from_payload_handles_null_body(): void
    {
        $result = ZarinpalPaymentRequestResult::fromPayload(null, 500);

        $this->assertFalse($result->ok);
        $this->assertNull($result->code);
        $this->assertSame('پاسخ زرین‌پال قابل خواندن نبود.', $result->message);
    }
}
