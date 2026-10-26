<?php

namespace App\Services\Contracts;

use App\DTO\Section\SectionCreateData;
use App\DTO\Section\SectionFilterData;
use App\DTO\Section\SectionItemFilterData;
use App\DTO\Section\SectionUpdateData;
use App\Models\Item;
use App\Models\Section;
use Illuminate\Contracts\Pagination\Paginator;

interface SectionServiceInterface
{
    public function create(SectionCreateData $data): Section;

    public function update(int $sectionId, SectionUpdateData $data): Section;

    public function details(int $sectionId): Section;

    public function filter(SectionFilterData $filters): Paginator;

    public function selectableItems(SectionItemFilterData $filters): Paginator;

    public function canSelectItem(int $itemId): bool;

    /** @return array{available: bool, reason: string} */
    public function homeAvailabilityForItem(Item $item): array;
}
