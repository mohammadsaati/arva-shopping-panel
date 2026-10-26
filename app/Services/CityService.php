<?php

namespace App\Services;

use App\DTO\City\CityCreateData;
use App\DTO\City\CityFilterData;
use App\DTO\City\CityUpdateData;
use App\Models\City;
use App\Models\Province;
use App\Services\Contracts\CityServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;

class CityService implements CityServiceInterface
{
    public function create(CityCreateData $data): City
    {
        return City::create($data);
    }

    public function details(int $cityId): City
    {
        return City::details($cityId);
    }

    public function update(int $cityId, CityUpdateData $data): City
    {
        $city = City::details($cityId);

        return $city->updateDetails($data);
    }

    public function filter(CityFilterData $filters): Paginator
    {
        return City::filter($filters);
    }

    public function formOptions(): array
    {
        return ['provinces' => Province::options()];
    }
}
