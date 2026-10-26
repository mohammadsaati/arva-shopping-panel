<?php

namespace App\DTO\Color;

final readonly class ColorUpdateData
{
    public function __construct(
        public ?string $name = null,
        public ?string $value = null,
    ) {
    }

    /** @return array<string, string> */
    public function toModelAttributes(): array
    {
        return array_filter([
            'name' => $this->name,
            'value' => $this->value,
        ], static fn (?string $value): bool => $value !== null);
    }
}
