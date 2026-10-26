<?php

namespace App\DTO\Province;

use App\Enums\GlobalStatusEnum;

final readonly class ProvinceUpdateData
{
    public function __construct(
        public ?string $name = null,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /** @return array<string, string|GlobalStatusEnum> */
    public function toModelAttributes(): array
    {
        return array_filter([
            'name' => $this->name,
            'status' => $this->status,
        ], static fn (string|GlobalStatusEnum|null $value): bool => $value !== null);
    }
}
