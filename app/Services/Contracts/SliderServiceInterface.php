<?php

namespace App\Services\Contracts;

use App\DTO\Slider\SliderCreateData;
use App\DTO\Slider\SliderFilterData;
use App\DTO\Slider\SliderUpdateData;
use App\Models\Slider;
use Illuminate\Contracts\Pagination\Paginator;

interface SliderServiceInterface
{
    public function create(SliderCreateData $data): Slider;

    public function update(int $sliderId, SliderUpdateData $data): Slider;

    public function details(int $sliderId): Slider;

    public function filter(SliderFilterData $filters): Paginator;

    /** @return array{products: array<int, string>, categories: array<int, string>, sections: array<int, string>} */
    public function formOptions(?int $productId = null, ?int $categoryId = null, ?int $sectionId = null): array;
}
