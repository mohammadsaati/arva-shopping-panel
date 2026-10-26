<?php

namespace App\Services;

use App\DTO\Section\SectionCreateData;
use App\DTO\Section\SectionFilterData;
use App\DTO\Section\SectionItemFilterData;
use App\DTO\Section\SectionUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Models\Item;
use App\Models\Section;
use App\Services\Contracts\SectionServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class SectionService implements SectionServiceInterface
{
    public function create(SectionCreateData $data): Section
    {
        $storedPaths = [];

        try {
            $bgImagePath = $this->storeOptionalImage($data->bgImage, 'sections/backgrounds');
            if ($bgImagePath !== null) {
                $storedPaths[] = $bgImagePath;
            }

            $imagePaths = $this->storeImages($data->images, 'sections/gallery');
            $storedPaths = [...$storedPaths, ...$imagePaths];

            return Section::create($data, $bgImagePath, $imagePaths);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }
    }

    public function update(int $sectionId, SectionUpdateData $data): Section
    {
        $section = Section::details($sectionId);
        $oldBgImagePath = $section->bg_image;
        $existingImageIds = $section->images->modelKeys();

        if (count(array_unique($data->deleteImageIds)) !== count($data->deleteImageIds)
            || array_diff($data->deleteImageIds, $existingImageIds) !== []) {
            throw ValidationException::withMessages(['delete_image_ids' => 'یکی از تصاویر انتخاب‌شده متعلق به این بخش نیست.']);
        }

        if (count($existingImageIds) - count($data->deleteImageIds) + count($data->images) > 10) {
            throw ValidationException::withMessages(['images' => 'هر بخش حداکثر می‌تواند ۱۰ تصویر گالری داشته باشد.']);
        }

        $storedPaths = [];
        $removedImagePaths = [];

        try {
            $bgImagePath = $this->storeOptionalImage($data->bgImage, 'sections/backgrounds');
            if ($bgImagePath !== null) {
                $storedPaths[] = $bgImagePath;
            }

            $imagePaths = $this->storeImages($data->images, 'sections/gallery');
            $storedPaths = [...$storedPaths, ...$imagePaths];

            $removedImagePaths = $section->updateDetails($data, $bgImagePath, $imagePaths);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }

        if (($bgImagePath !== null || $data->removeBgImage) && $oldBgImagePath !== null) {
            Storage::disk('public')->delete($oldBgImagePath);
        }

        Storage::disk('public')->delete($removedImagePaths);

        return Section::details($sectionId);
    }

    public function details(int $sectionId): Section
    {
        return Section::details($sectionId);
    }

    public function filter(SectionFilterData $filters): Paginator
    {
        return Section::filter($filters);
    }

    public function selectableItems(SectionItemFilterData $filters): Paginator
    {
        return Item::selectableForSection($filters);
    }

    public function canSelectItem(int $itemId): bool
    {
        return Item::isActiveForSection($itemId);
    }

    public function homeAvailabilityForItem(Item $item): array
    {
        if ($item->status !== GlobalStatusEnum::ACTIVE) {
            return ['available' => false, 'reason' => 'آیتم غیرفعال است.'];
        }

        if ($item->category?->status !== GlobalStatusEnum::ACTIVE) {
            return ['available' => false, 'reason' => 'دسته‌بندی فعال ندارد.'];
        }

        if (! (bool) $item->getAttribute('has_available_product')) {
            return ['available' => false, 'reason' => 'محصول فعال و دارای موجودی ندارد.'];
        }

        return ['available' => true, 'reason' => 'محصول فعال و دارای موجودی دارد.'];
    }

    private function storeOptionalImage(?UploadedFile $image, string $directory): ?string
    {
        if ($image === null) {
            return null;
        }

        $path = $image->store($directory, 'public');
        if (! is_string($path)) {
            throw new RuntimeException('The section image could not be saved.');
        }

        return $path;
    }

    /** @param array<int, UploadedFile> $images
     *  @return array<int, string>
     */
    private function storeImages(array $images, string $directory): array
    {
        $paths = [];

        try {
            foreach ($images as $image) {
                $path = $image->store($directory, 'public');
                if (! is_string($path)) {
                    throw new RuntimeException('A section gallery image could not be saved.');
                }

                $paths[] = $path;
            }
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($paths);
            throw $exception;
        }

        return $paths;
    }
}
