<?php

namespace App\Services\Contracts;

use App\DTO\Category\CategoryCreateData;
use App\DTO\Category\CategoryFilterData;
use App\DTO\Category\CategorySelectOptionsData;
use App\DTO\Category\CategoryUpdateData;
use App\Models\Category;
use Illuminate\Contracts\Pagination\Paginator;

interface CategoryServiceInterface
{
    public function create(CategoryCreateData $data): Category;

    public function update(int $categoryId, CategoryUpdateData $data): Category;

    public function details(int $categoryId): Category;

    public function filter(CategoryFilterData $filters): Paginator;

    /** @return array{results: array<int, array{id: int, text: string}>, pagination: array{more: bool}} */
    public function selectOptions(CategorySelectOptionsData $filters): array;

    /** @return array{parents: array<int, string>} */
    public function formOptions(?int $excludeCategoryId = null): array;
}
