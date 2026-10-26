<?php

namespace App\DTO\Size;

final readonly class SizeUpdateData
{
    public function __construct(public ?string $value = null)
    {
    }

    /** @return array<string, string> */
    public function toModelAttributes(): array
    {
        return array_filter(['value' => $this->value], static fn (?string $value): bool => $value !== null);
    }
}
