<?php

namespace App\Services\Contracts;

use App\DTO\Product\ProductCreateData;
use App\DTO\Product\ProductFilterData;
use App\DTO\Product\ProductUpdateData;
use App\Models\Product;
use Illuminate\Contracts\Pagination\Paginator;

interface ProductServiceInterface
{
    public function create(ProductCreateData $data): Product;

    public function details(int $productId): Product;

    public function update(int $productId, ProductUpdateData $data): Product;

    public function filter(ProductFilterData $filters): Paginator;

    /** @return array{items: array<int, string>, hasItems: bool, colors: array<int, string>, sizes: array<int, string>, cities: array<string, array<int, string>>} */
    public function formOptions(?int $includeItemId = null): array;

    /** @return array<int, string> */
    public function itemOptions(?int $selectedItemId = null): array;

    /** @return array<int, string> */
    public function colorFilterOptions(): array;

    /** @return array<int, string> */
    public function sizeFilterOptions(): array;
}
