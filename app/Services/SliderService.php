<?php

namespace App\Services;

use App\DTO\Slider\SliderCreateData;
use App\DTO\Slider\SliderFilterData;
use App\DTO\Slider\SliderUpdateData;
use App\Models\Category;
use App\Models\Product;
use App\Models\Section;
use App\Models\Slider;
use App\Services\Contracts\SliderServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class SliderService implements SliderServiceInterface
{
    public function create(SliderCreateData $data): Slider
    {
        $imagePath = $data->image->store('sliders', 'public');
        if (! is_string($imagePath)) {
            throw new RuntimeException('The slider image could not be saved.');
        }

        try {
            return Slider::create($data, $imagePath);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($imagePath);
            throw $exception;
        }
    }

    public function update(int $sliderId, SliderUpdateData $data): Slider
    {
        $slider = Slider::details($sliderId);
        $oldImagePath = $slider->image;
        $newImagePath = $data->image?->store('sliders', 'public');

        if ($data->image !== null && ! is_string($newImagePath)) {
            throw new RuntimeException('The slider image could not be saved.');
        }

        try {
            $slider->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if (is_string($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if (is_string($newImagePath) && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $slider;
    }

    public function details(int $sliderId): Slider
    {
        return Slider::details($sliderId);
    }

    public function filter(SliderFilterData $filters): Paginator
    {
        return Slider::filter($filters);
    }

    public function formOptions(?int $productId = null, ?int $categoryId = null, ?int $sectionId = null): array
    {
        return [
            'products' => Product::adminOptions($productId),
            'categories' => Category::activeOptions(includeCategoryId: $categoryId),
            'sections' => Section::activeOptions($sectionId),
        ];
    }
}
