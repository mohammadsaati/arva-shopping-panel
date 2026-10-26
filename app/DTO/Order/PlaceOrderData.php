<?php

namespace App\DTO\Order;

final readonly class PlaceOrderData
{
    public function __construct(
        public int $addressId,
        public ?string $discountCode = null,
        public ?int $paymentGatewayId = null,
    ) {
    }
}
