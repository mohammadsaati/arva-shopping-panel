<?php

namespace App\DTO\Specification;

final readonly class SpecificationGroupData
{
    public function __construct(
        public string $name,
        public int $sortOrder,
    ) {
    }
}
