<?php

namespace App\Enums;

enum PaymentGatewayDriverEnum: string
{
    use LabelTrait, ValuesTrait;

    case ZARINPAL = 'ZARINPAL';

    protected function labelKey(): string
    {
        return 'payment_gateway_driver';
    }

    public function defaultImage(): string
    {
        return match ($this) {
            self::ZARINPAL => 'assets/media/payments/zarinpal.svg',
        };
    }
}
