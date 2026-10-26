<?php

namespace App\DTO\Color;

final readonly class ColorCreateData
{
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }
}
