<?php

namespace App\Services\Contracts;

use App\DTO\Province\ProvinceCreateData;
use App\DTO\Province\ProvinceFilterData;
use App\DTO\Province\ProvinceUpdateData;
use App\Models\Province;
use Illuminate\Contracts\Pagination\Paginator;

interface ProvinceServiceInterface
{
    public function create(ProvinceCreateData $data): Province;

    public function details(int $provinceId): Province;

    public function update(int $provinceId, ProvinceUpdateData $data): Province;

    public function filter(ProvinceFilterData $filters): Paginator;
}
