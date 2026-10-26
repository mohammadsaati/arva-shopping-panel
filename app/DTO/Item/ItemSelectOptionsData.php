<?php

namespace App\DTO\Item;

final readonly class ItemSelectOptionsData
{
    /** @param array<int, int> $selectedIds */
    public function __construct(
        public string $search,
        public array $selectedIds = [],
        public int $page = 1,
    ) {
    }
}
