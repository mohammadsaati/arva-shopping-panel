<?php

namespace App\Services\Contracts;

use App\DTO\Brand\BrandCreateData;
use App\DTO\Brand\BrandFilterData;
use App\DTO\Brand\BrandUpdateData;
use App\Models\Brand;
use Illuminate\Contracts\Pagination\Paginator;

interface BrandServiceInterface
{
    public function create(BrandCreateData $data): Brand;

    public function details(int $brandId): Brand;

    public function update(int $brandId, BrandUpdateData $data): Brand;

    public function filter(BrandFilterData $filters): Paginator;
}
