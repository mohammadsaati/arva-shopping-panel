<?php

namespace App\DTO\Category;

final readonly class CategorySelectOptionsData
{
    /** @param array<int, int> $selectedIds */
    public function __construct(
        public string $search,
        public array $selectedIds = [],
        public int $page = 1,
    ) {
    }
}
