<?php

namespace Tests\Unit\Payment;

use App\Services\ZarinpalClient;
use PHPUnit\Framework\TestCase;

class ZarinpalClientEndpointsTest extends TestCase
{
    public function test_request_url_uses_sandbox_host(): void
    {
        $this->assertSame(
            'https://sandbox.zarinpal.com/pg/v4/payment/request.json',
            ZarinpalClient::requestUrl(true),
        );
    }

    public function test_request_url_uses_production_host(): void
    {
        $this->assertSame(
            'https://payment.zarinpal.com/pg/v4/payment/request.json',
            ZarinpalClient::requestUrl(false),
        );
    }

    public function test_verify_url_uses_sandbox_host(): void
    {
        $this->assertSame(
            'https://sandbox.zarinpal.com/pg/v4/payment/verify.json',
            ZarinpalClient::verifyUrl(true),
        );
    }

    public function test_verify_url_uses_production_host(): void
    {
        $this->assertSame(
            'https://payment.zarinpal.com/pg/v4/payment/verify.json',
            ZarinpalClient::verifyUrl(false),
        );
    }

    public function test_payment_page_url_appends_authority(): void
    {
        $this->assertSame(
            'https://payment.zarinpal.com/pg/StartPay/A0000000000000000000000000000wwOGYpd',
            ZarinpalClient::paymentPageUrl('A0000000000000000000000000000wwOGYpd', false),
        );
        $this->assertSame(
            'https://sandbox.zarinpal.com/pg/StartPay/A0000000000000000000000000000wwOGYpd',
            ZarinpalClient::paymentPageUrl('A0000000000000000000000000000wwOGYpd', true),
        );
    }

    public function test_start_pay_url_matches_payment_page(): void
    {
        $client = new ZarinpalClient();

        $this->assertSame(
            ZarinpalClient::paymentPageUrl('A123', true),
            $client->startPayUrl('A123', true),
        );
    }
}
