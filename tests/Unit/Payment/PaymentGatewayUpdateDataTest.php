<?php

namespace Tests\Unit\Payment;

use App\DTO\Payment\PaymentGatewaySettings;
use App\DTO\Payment\PaymentGatewayUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use PHPUnit\Framework\TestCase;

class PaymentGatewayUpdateDataTest extends TestCase
{
    public function test_to_model_attributes_skips_null_fields(): void
    {
        $data = new PaymentGatewayUpdateData();

        $this->assertSame([], $data->toModelAttributes());
    }

    public function test_to_model_attributes_includes_provided_name_and_status(): void
    {
        $data = new PaymentGatewayUpdateData(
            name: 'پرداخت زرین‌پال',
            status: GlobalStatusEnum::ACTIVE,
        );

        $this->assertSame([
            'name' => 'پرداخت زرین‌پال',
            'status' => GlobalStatusEnum::ACTIVE,
        ], $data->toModelAttributes());
    }

    public function test_to_model_attributes_does_not_include_image_fields(): void
    {
        $data = new PaymentGatewayUpdateData(
            name: 'زرین‌پال',
            removeImage: true,
        );

        $this->assertSame(['name' => 'زرین‌پال'], $data->toModelAttributes());
        $this->assertTrue($data->removeImage);
        $this->assertNull($data->image);
    }

    public function test_applied_settings_merges_only_provided_keys(): void
    {
        $data = new PaymentGatewayUpdateData(
            merchantId: '1344b5d4-0048-11e8-94db-005056a205be',
            merchantIdProvided: true,
            sandbox: false,
            sandboxProvided: true,
        );

        $this->assertEquals(
            new PaymentGatewaySettings(
                merchantId: '1344b5d4-0048-11e8-94db-005056a205be',
                sandbox: false,
                currency: PaymentCurrencyEnum::IRT,
            ),
            $data->appliedSettings(new PaymentGatewaySettings(
                merchantId: '',
                sandbox: true,
                currency: PaymentCurrencyEnum::IRT,
            )),
        );
    }

    public function test_applied_settings_can_change_currency(): void
    {
        $data = new PaymentGatewayUpdateData(
            currency: PaymentCurrencyEnum::IRR,
            currencyProvided: true,
        );

        $this->assertEquals(
            new PaymentGatewaySettings(
                merchantId: '',
                sandbox: false,
                currency: PaymentCurrencyEnum::IRR,
            ),
            $data->appliedSettings(PaymentGatewaySettings::empty()),
        );
    }
}
