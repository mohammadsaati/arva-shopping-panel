<?php

namespace App\Services;

use App\DTO\Product\ProductCreateData;
use App\DTO\Product\ProductFilterData;
use App\DTO\Product\ProductUpdateData;
use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ProductService implements ProductServiceInterface
{
    public function create(ProductCreateData $data): Product
    {
        if (count($data->images) > 10) {
            throw ValidationException::withMessages(['images' => 'هر محصول حداکثر می‌تواند ۱۰ تصویر داشته باشد.']);
        }

        $paths = [];

        try {
            $paths = $this->storeImages($data->images);

            return Product::create($data, $paths);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($paths);
            throw $exception;
        }
    }

    public function filter(ProductFilterData $filters): Paginator
    {
        return Product::filter($filters);
    }

    public function details(int $productId): Product
    {
        return Product::details($productId);
    }

    public function update(int $productId, ProductUpdateData $data): Product
    {
        $product = Product::details($productId);
        $existingIds = $product->images->pluck('id')->all();

        foreach (array_keys($data->replacements) as $imageId) {
            if (! ctype_digit((string) $imageId)) {
                throw ValidationException::withMessages(['replacements' => 'شناسه تصویر انتخاب‌شده معتبر نیست.']);
            }
        }

        $replacementIds = array_map('intval', array_keys($data->replacements));

        if (array_diff([...$replacementIds, ...$data->deleteImageIds], $existingIds) !== []) {
            throw ValidationException::withMessages(['images' => 'یکی از تصاویر انتخاب‌شده متعلق به این محصول نیست.']);
        }

        if (array_intersect($replacementIds, $data->deleteImageIds) !== []) {
            throw ValidationException::withMessages(['images' => 'یک تصویر را نمی‌توان هم‌زمان جایگزین و حذف کرد.']);
        }

        if (count($existingIds) - count($data->deleteImageIds) + count($data->images) > 10) {
            throw ValidationException::withMessages(['images' => 'هر محصول حداکثر می‌تواند ۱۰ تصویر داشته باشد.']);
        }

        $newPaths = [];
        $replacementPaths = [];

        try {
            $newPaths = $this->storeImages($data->images);

            foreach ($data->replacements as $imageId => $file) {
                $replacementPaths[(int) $imageId] = $this->storeImage($file);
            }

            $oldPaths = $product->updateDetails($data, $newPaths, $replacementPaths);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete([...$newPaths, ...array_values($replacementPaths)]);
            throw $exception;
        }

        Storage::disk('public')->delete(array_values(array_filter(
            $oldPaths,
            static fn (string $path): bool => str_starts_with($path, 'products/gallery/'),
        )));

        return Product::details($productId);
    }

    public function formOptions(?int $includeItemId = null): array
    {
        return Product::formOptions($includeItemId);
    }

    public function itemOptions(?int $selectedItemId = null): array
    {
        return Product::itemOptions($selectedItemId);
    }

    public function colorFilterOptions(): array
    {
        return Product::colorFilterOptions();
    }

    public function sizeFilterOptions(): array
    {
        return Product::sizeFilterOptions();
    }

    /**
     * @param array<int, UploadedFile> $images
     * @return array<int, string>
     */
    private function storeImages(array $images): array
    {
        $paths = [];

        try {
            foreach ($images as $image) {
                $paths[] = $this->storeImage($image);
            }
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($paths);
            throw $exception;
        }

        return $paths;
    }

    private function storeImage(UploadedFile $image): string
    {
        $path = $image->store('products/gallery', 'public');

        if (! is_string($path)) {
            throw new RuntimeException('The product image could not be saved.');
        }

        return $path;
    }
}
