<?php

namespace App\Services\Contracts;

use App\DTO\Size\SizeCreateData;
use App\DTO\Size\SizeFilterData;
use App\DTO\Size\SizeUpdateData;
use App\Models\Size;
use Illuminate\Contracts\Pagination\Paginator;

interface SizeServiceInterface
{
    public function create(SizeCreateData $data): Size;

    public function details(int $sizeId): Size;

    public function update(int $sizeId, SizeUpdateData $data): Size;

    public function filter(SizeFilterData $filters): Paginator;
}
