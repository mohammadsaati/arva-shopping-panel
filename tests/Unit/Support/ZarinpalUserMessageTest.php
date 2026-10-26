<?php

namespace Tests\Unit\Support;

use App\Support\ZarinpalUserMessage;
use PHPUnit\Framework\TestCase;

class ZarinpalUserMessageTest extends TestCase
{
    public function test_maps_known_request_error_code_to_persian(): void
    {
        $this->assertSame(
            'پرداخت توسط شما لغو شد.',
            ZarinpalUserMessage::forRequest(-53, 'User cancelled'),
        );
    }

    public function test_replaces_english_fallback_with_generic_persian(): void
    {
        $message = ZarinpalUserMessage::forVerify(-999, 'Unknown gateway error');

        $this->assertSame(
            'تأیید پرداخت انجام نشد. اگر مبلغ از حساب شما کسر شده، چند دقیقه صبر کنید یا با پشتیبانی تماس بگیرید.',
            $message,
        );
    }

    public function test_keeps_persian_gateway_message(): void
    {
        $this->assertSame(
            'تراکنش قبلاً تأیید شده',
            ZarinpalUserMessage::forVerify(null, 'تراکنش قبلاً تأیید شده'),
        );
    }
}
