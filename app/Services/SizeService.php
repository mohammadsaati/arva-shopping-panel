<?php

namespace App\Services;

use App\DTO\Size\SizeCreateData;
use App\DTO\Size\SizeFilterData;
use App\DTO\Size\SizeUpdateData;
use App\Models\Size;
use App\Services\Contracts\SizeServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;

class SizeService implements SizeServiceInterface
{
    public function create(SizeCreateData $data): Size
    {
        return Size::create($data);
    }

    public function filter(SizeFilterData $filters): Paginator
    {
        return Size::filter($filters);
    }

    public function details(int $sizeId): Size
    {
        return Size::details($sizeId);
    }

    public function update(int $sizeId, SizeUpdateData $data): Size
    {
        $size = Size::details($sizeId);

        return $size->updateDetails($data);
    }
}
