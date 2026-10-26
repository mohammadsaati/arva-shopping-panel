<?php

namespace App\Services;

use App\DTO\Brand\BrandCreateData;
use App\DTO\Brand\BrandFilterData;
use App\DTO\Brand\BrandUpdateData;
use App\Models\Brand;
use App\Services\Contracts\BrandServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class BrandService implements BrandServiceInterface
{
    public function create(BrandCreateData $data): Brand
    {
        $imagePath = null;

        if ($data->image !== null) {
            $storedImagePath = $data->image->store('brands', 'public');

            if (! is_string($storedImagePath)) {
                throw new RuntimeException('The brand image could not be saved.');
            }

            $imagePath = $storedImagePath;
        }

        try {
            return Brand::create($data, $imagePath);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function details(int $brandId): Brand
    {
        return Brand::details($brandId);
    }

    public function update(int $brandId, BrandUpdateData $data): Brand
    {
        $brand = Brand::details($brandId);
        $oldImagePath = $brand->image;
        $newImagePath = null;

        if ($data->image !== null) {
            $storedImagePath = $data->image->store('brands', 'public');

            if (! is_string($storedImagePath)) {
                throw new RuntimeException('The brand image could not be saved.');
            }

            $newImagePath = $storedImagePath;
        }

        try {
            $brand->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath !== null && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $brand;
    }

    public function filter(BrandFilterData $filters): Paginator
    {
        return Brand::filter($filters);
    }
}
