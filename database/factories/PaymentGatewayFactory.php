<?php

namespace Database\Factories;

use App\DTO\Payment\PaymentGatewaySettings;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use App\Enums\PaymentGatewayDriverEnum;
use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentGateway>
 */
class PaymentGatewayFactory extends Factory
{
    protected $model = PaymentGateway::class;

    public function definition(): array
    {
        return [
            'driver' => PaymentGatewayDriverEnum::ZARINPAL,
            'name' => 'زرین‌پال',
            'image' => null,
            'status' => GlobalStatusEnum::INACTIVE,
            'settings' => new PaymentGatewaySettings(
                merchantId: '',
                sandbox: true,
                currency: PaymentCurrencyEnum::IRT,
            ),
        ];
    }
}
