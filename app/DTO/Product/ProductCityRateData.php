<?php

namespace App\DTO\Product;

final readonly class ProductCityRateData
{
    public function __construct(
        public int $cityId,
        public int $shippingPrice,
    ) {}
}
