<?php

namespace App\Enums;

enum PaymentCurrencyEnum: string
{
    use LabelTrait, ValuesTrait;

    case IRT = 'IRT';
    case IRR = 'IRR';

    protected function labelKey(): string
    {
        return 'payment_currency';
    }
}
