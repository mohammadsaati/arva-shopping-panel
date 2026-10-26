<?php

namespace Tests\Unit\Payment;

use App\DTO\Payment\PaymentGatewaySettings;
use App\Enums\PaymentCurrencyEnum;
use PHPUnit\Framework\TestCase;

class PaymentGatewaySettingsTest extends TestCase
{
    public function test_from_array_maps_typed_fields(): void
    {
        $settings = PaymentGatewaySettings::from([
            'merchant_id' => '  1344b5d4-0048-11e8-94db-005056a205be  ',
            'sandbox' => 1,
            'currency' => PaymentCurrencyEnum::IRR->value,
        ]);

        $this->assertSame('1344b5d4-0048-11e8-94db-005056a205be', $settings->merchantId);
        $this->assertTrue($settings->sandbox);
        $this->assertSame(PaymentCurrencyEnum::IRR, $settings->currency);
    }

    public function test_from_json_and_null_use_defaults(): void
    {
        $fromJson = PaymentGatewaySettings::from('{"merchant_id":"abc","sandbox":true,"currency":"IRT"}');
        $fromNull = PaymentGatewaySettings::from(null);

        $this->assertSame('abc', $fromJson->merchantId);
        $this->assertTrue($fromJson->sandbox);
        $this->assertSame(PaymentCurrencyEnum::IRT, $fromJson->currency);
        $this->assertEquals(PaymentGatewaySettings::empty(), $fromNull);
    }

    public function test_to_array_uses_storage_keys(): void
    {
        $settings = new PaymentGatewaySettings(
            merchantId: '1344b5d4-0048-11e8-94db-005056a205be',
            sandbox: true,
            currency: PaymentCurrencyEnum::IRR,
        );

        $this->assertSame([
            'merchant_id' => '1344b5d4-0048-11e8-94db-005056a205be',
            'sandbox' => true,
            'currency' => 'IRR',
        ], $settings->toArray());
    }

    public function test_merge_keeps_omitted_fields(): void
    {
        $current = new PaymentGatewaySettings(
            merchantId: 'old-id',
            sandbox: true,
            currency: PaymentCurrencyEnum::IRT,
        );

        $this->assertEquals(
            new PaymentGatewaySettings(
                merchantId: 'old-id',
                sandbox: false,
                currency: PaymentCurrencyEnum::IRT,
            ),
            $current->merge(sandbox: false, sandboxProvided: true),
        );
    }
}
