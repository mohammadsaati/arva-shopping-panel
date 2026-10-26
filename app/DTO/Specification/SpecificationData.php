<?php

namespace App\DTO\Specification;

use App\Enums\SpecificationTypeEnum;

final readonly class SpecificationData
{
    public function __construct(
        public int $groupId,
        public string $key,
        public string $name,
        public SpecificationTypeEnum $type,
        public ?string $unit,
        public bool $isRequired,
        public bool $isHighlighted,
        public bool $isFilterable,
        public int $sortOrder,
        public ?string $options,
    ) {
    }
}
