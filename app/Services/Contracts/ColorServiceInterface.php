<?php

namespace App\Services\Contracts;

use App\DTO\Color\ColorCreateData;
use App\DTO\Color\ColorFilterData;
use App\DTO\Color\ColorUpdateData;
use App\Models\Color;
use Illuminate\Contracts\Pagination\Paginator;

interface ColorServiceInterface
{
    public function create(ColorCreateData $data): Color;

    public function details(int $colorId): Color;

    public function update(int $colorId, ColorUpdateData $data): Color;

    public function filter(ColorFilterData $filters): Paginator;
}
