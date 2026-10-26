<?php

namespace App\DTO\City;

use App\Enums\GlobalStatusEnum;

final readonly class CityUpdateData
{
    public function __construct(
        public ?int $provinceId = null,
        public ?string $name = null,
        public ?string $slug = null,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /** @return array<string, int|string|GlobalStatusEnum> */
    public function toModelAttributes(): array
    {
        return array_filter([
            'province_id' => $this->provinceId,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
        ], static fn (int|string|GlobalStatusEnum|null $value): bool => $value !== null);
    }
}
