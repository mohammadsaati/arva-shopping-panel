<?php

namespace App\Services;

use App\DTO\Color\ColorCreateData;
use App\DTO\Color\ColorFilterData;
use App\DTO\Color\ColorUpdateData;
use App\Models\Color;
use App\Services\Contracts\ColorServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;

class ColorService implements ColorServiceInterface
{
    public function create(ColorCreateData $data): Color
    {
        return Color::create($data);
    }

    public function filter(ColorFilterData $filters): Paginator
    {
        return Color::filter($filters);
    }

    public function details(int $colorId): Color
    {
        return Color::details($colorId);
    }

    public function update(int $colorId, ColorUpdateData $data): Color
    {
        $color = Color::details($colorId);

        return $color->updateDetails($data);
    }
}
