<?php

namespace App\Services;

use App\DTO\Banner\BannerCreateData;
use App\DTO\Banner\BannerFilterData;
use App\DTO\Banner\BannerUpdateData;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Services\Contracts\BannerServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class BannerService implements BannerServiceInterface
{
    public function create(BannerCreateData $data): Banner
    {
        $imagePath = $data->image->store('banners', 'public');
        if (! is_string($imagePath)) {
            throw new RuntimeException('The banner image could not be saved.');
        }

        try {
            return Banner::create($data, $imagePath);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($imagePath);
            throw $exception;
        }
    }

    public function update(int $bannerId, BannerUpdateData $data): Banner
    {
        $banner = Banner::details($bannerId);
        $oldImagePath = $banner->image;
        $newImagePath = $data->image?->store('banners', 'public');

        if ($data->image !== null && ! is_string($newImagePath)) {
            throw new RuntimeException('The banner image could not be saved.');
        }

        try {
            $banner->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if (is_string($newImagePath)) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if (is_string($newImagePath) && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $banner;
    }

    public function details(int $bannerId): Banner
    {
        return Banner::details($bannerId);
    }

    public function filter(BannerFilterData $filters): Paginator
    {
        return Banner::filter($filters);
    }

    public function formOptions(?int $productId = null, ?int $categoryId = null): array
    {
        return [
            'products' => Product::adminOptions($productId),
            'categories' => Category::activeOptions(includeCategoryId: $categoryId),
        ];
    }
}
