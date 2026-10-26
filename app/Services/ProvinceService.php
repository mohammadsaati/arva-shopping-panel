<?php

namespace App\Services;

use App\DTO\Province\ProvinceCreateData;
use App\DTO\Province\ProvinceFilterData;
use App\DTO\Province\ProvinceUpdateData;
use App\Models\Province;
use App\Services\Contracts\ProvinceServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;

class ProvinceService implements ProvinceServiceInterface
{
    public function create(ProvinceCreateData $data): Province
    {
        return Province::create($data);
    }

    public function details(int $provinceId): Province
    {
        return Province::details($provinceId);
    }

    public function update(int $provinceId, ProvinceUpdateData $data): Province
    {
        $province = Province::details($provinceId);

        return $province->updateDetails($data);
    }

    public function filter(ProvinceFilterData $filters): Paginator
    {
        return Province::filter($filters);
    }
}
