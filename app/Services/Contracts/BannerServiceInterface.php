<?php

namespace App\Services\Contracts;

use App\DTO\Banner\BannerCreateData;
use App\DTO\Banner\BannerFilterData;
use App\DTO\Banner\BannerUpdateData;
use App\Models\Banner;
use Illuminate\Contracts\Pagination\Paginator;

interface BannerServiceInterface
{
    public function create(BannerCreateData $data): Banner;

    public function update(int $bannerId, BannerUpdateData $data): Banner;

    public function details(int $bannerId): Banner;

    public function filter(BannerFilterData $filters): Paginator;

    /** @return array{products: array<int, string>, categories: array<int, string>} */
    public function formOptions(?int $productId = null, ?int $categoryId = null): array;
}
