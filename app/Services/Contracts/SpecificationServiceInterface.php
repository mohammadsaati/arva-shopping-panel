<?php

namespace App\Services\Contracts;

use App\DTO\Specification\SpecificationData;
use App\DTO\Specification\SpecificationGroupData;
use App\DTO\Specification\SpecificationCategoryFilterData;
use App\Models\Category;
use App\Models\Specification;
use App\Models\SpecificationGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

interface SpecificationServiceInterface
{
    public function categories(SpecificationCategoryFilterData $filters): Paginator;

    public function category(int $categoryId): Category;

    /** @return Collection<int, SpecificationGroup> */
    public function groups(int $categoryId): Collection;

    public function createGroup(int $categoryId, SpecificationGroupData $data): SpecificationGroup;

    public function updateGroup(int $categoryId, int $groupId, SpecificationGroupData $data): SpecificationGroup;

    public function deleteGroup(int $categoryId, int $groupId): void;

    public function createSpecification(int $categoryId, SpecificationData $data): Specification;

    public function specification(int $categoryId, int $specificationId): Specification;

    public function updateSpecification(int $categoryId, int $specificationId, SpecificationData $data): Specification;

    public function deleteSpecification(int $categoryId, int $specificationId): void;
}
