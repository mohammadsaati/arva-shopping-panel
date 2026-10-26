<?php

namespace App\Services;

use App\DTO\Item\ItemCreateData;
use App\DTO\Item\ItemFilterData;
use App\DTO\Item\ItemSelectOptionsData;
use App\DTO\Item\ItemUpdateData;
use App\Models\Item;
use App\Models\SpecificationGroup;
use App\Services\Contracts\ItemServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class ItemService implements ItemServiceInterface
{
    public function create(ItemCreateData $data): Item
    {
        $imagePath = null;

        if ($data->image !== null) {
            $storedImagePath = $data->image->store('items', 'public');

            if (! is_string($storedImagePath)) {
                throw new RuntimeException('The item image could not be saved.');
            }

            $imagePath = $storedImagePath;
        }

        try {
            return Item::create($data, $imagePath);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function update(int $itemId, ItemUpdateData $data): Item
    {
        $item = Item::details($itemId);
        $oldImagePath = $item->image;
        $newImagePath = null;

        if ($data->image !== null) {
            $storedImagePath = $data->image->store('items', 'public');

            if (! is_string($storedImagePath)) {
                throw new RuntimeException('The item image could not be saved.');
            }

            $newImagePath = $storedImagePath;
        }

        try {
            $item->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if ($newImagePath !== null && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $item;
    }

    public function details(int $itemId): Item
    {
        return Item::details($itemId);
    }

    public function filter(ItemFilterData $filters): Paginator
    {
        return Item::filter($filters);
    }

    public function selectOptions(ItemSelectOptionsData $filters): array
    {
        return Item::selectOptions($filters);
    }

    public function formOptions(?Item $currentItem = null): array
    {
        return Item::formOptions($currentItem);
    }

    public function specificationGroups(int $categoryId): Collection
    {
        return SpecificationGroup::forCategory($categoryId);
    }
}
