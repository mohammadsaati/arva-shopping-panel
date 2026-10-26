<?php

namespace App\Services;

use App\DTO\Category\CategoryCreateData;
use App\DTO\Category\CategoryFilterData;
use App\DTO\Category\CategorySelectOptionsData;
use App\DTO\Category\CategoryUpdateData;
use App\Models\Category;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class CategoryService implements CategoryServiceInterface
{
    public function create(CategoryCreateData $data): Category
    {
        $imagePath = null;

        if ($data->image !== null) {
            $storedImagePath = $data->image->store('categories', 'public');

            if (! is_string($storedImagePath)) {
                throw new RuntimeException('The category image could not be saved.');
            }

            $imagePath = $storedImagePath;
        }

        try {
            return Category::create($data, $imagePath);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function update(int $categoryId, CategoryUpdateData $data): Category
    {
        $category = Category::details($categoryId);
        $oldImagePath = $category->image;
        $newImagePath = null;

        if ($data->image !== null) {
            $storedImagePath = $data->image->store('categories', 'public');

            if (! is_string($storedImagePath)) {
                throw new RuntimeException('The category image could not be saved.');
            }

            $newImagePath = $storedImagePath;
        }

        try {
            $category->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath !== null && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $category;
    }

    public function details(int $categoryId): Category
    {
        return Category::details($categoryId);
    }

    public function filter(CategoryFilterData $filters): Paginator
    {
        return Category::filter($filters);
    }

    /** @return array{results: array<int, array{id: int, text: string}>, pagination: array{more: bool}} */
    public function selectOptions(CategorySelectOptionsData $filters): array
    {
        return Category::selectOptions($filters);
    }

    public function formOptions(?int $excludeCategoryId = null): array
    {
        return Category::formOptions($excludeCategoryId);
    }
}
