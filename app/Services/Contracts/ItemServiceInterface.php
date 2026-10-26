<?php

namespace App\Services\Contracts;

use App\DTO\Item\ItemCreateData;
use App\DTO\Item\ItemFilterData;
use App\DTO\Item\ItemSelectOptionsData;
use App\DTO\Item\ItemUpdateData;
use App\Models\Item;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\SpecificationGroup;

interface ItemServiceInterface
{
    public function create(ItemCreateData $data): Item;

    public function update(int $itemId, ItemUpdateData $data): Item;

    public function details(int $itemId): Item;

    public function filter(ItemFilterData $filters): Paginator;

    /** @return array{results: array<int, array{id: int, text: string}>, pagination: array{more: bool}} */
    public function selectOptions(ItemSelectOptionsData $filters): array;

    /** @return array{brands: array<int, string>, colors: array<int, string>, sizes: array<int, string>, cities: array<string, array<int, string>>} */
    public function formOptions(?Item $currentItem = null): array;

    /** @return Collection<int, SpecificationGroup> */
    public function specificationGroups(int $categoryId): Collection;
}
