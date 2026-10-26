<?php

namespace App\DTO\Specification;

final readonly class SpecificationAnswerData
{
    /** @param array<int, int> $optionIds */
    public function __construct(
        public int $specificationId,
        public ?string $text = null,
        public ?string $number = null,
        public ?bool $boolean = null,
        public array $optionIds = [],
    ) {
    }
}
