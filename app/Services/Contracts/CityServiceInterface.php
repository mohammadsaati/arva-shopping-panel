<?php

namespace App\Services\Contracts;

use App\DTO\City\CityCreateData;
use App\DTO\City\CityFilterData;
use App\DTO\City\CityUpdateData;
use App\Models\City;
use Illuminate\Contracts\Pagination\Paginator;

interface CityServiceInterface
{
    public function create(CityCreateData $data): City;

    public function details(int $cityId): City;

    public function update(int $cityId, CityUpdateData $data): City;

    public function filter(CityFilterData $filters): Paginator;

    /** @return array{provinces: array<int, string>} */
    public function formOptions(): array;
}
