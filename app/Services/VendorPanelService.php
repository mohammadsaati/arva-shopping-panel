<?php

namespace App\Services;

use App\DTO\Item\ItemCreateData;
use App\DTO\Product\ProductCreateData;
use App\DTO\VendorPanel\VendorItemCreateData;
use App\DTO\VendorPanel\VendorItemFilterData;
use App\DTO\VendorPanel\VendorOrderFilterData;
use App\DTO\VendorPanel\VendorProductCreateData;
use App\DTO\VendorPanel\VendorProductFilterData;
use App\Enums\GlobalStatusEnum;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Item;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Models\VendorOrder;
use App\Services\Contracts\ItemServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\VendorPanelServiceInterface;
use App\Services\Contracts\VendorServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

class VendorPanelService implements VendorPanelServiceInterface
{
    public function __construct(
        private readonly VendorServiceInterface $vendors,
        private readonly ItemServiceInterface $itemsService,
        private readonly ProductServiceInterface $productsService,
    ) {
    }

    public function dashboard(User $user): array
    {
        $vendorId = $this->vendors->profileForUser($user)->id;

        return [
            'items' => Item::countForVendor($vendorId),
            'products' => Product::countForVendor($vendorId),
            'orders' => VendorOrder::countForVendor($vendorId),
        ];
    }

    public function itemFormOptions(): array
    {
        return [
            'categories' => Category::activeOptions(),
            'brands' => Brand::activeOptions(),
        ];
    }

    public function productFormOptions(User $user): array
    {
        $vendorId = $this->vendors->profileForUser($user)->id;

        return [
            'items' => Item::optionsForVendor($vendorId),
            'colors' => Color::options(),
            'sizes' => Size::options(),
        ];
    }

    public function createItem(User $user, VendorItemCreateData $data): Item
    {
        $vendorId = $this->vendors->profileForUser($user)->id;

        return $this->itemsService->create(new ItemCreateData(
            name: $data->name,
            slug: $data->slug,
            code: $data->code,
            categoryId: $data->categoryId,
            brandId: $data->brandId,
            image: $data->image,
            description: $data->description,
            status: GlobalStatusEnum::ACTIVE,
            vendorId: $vendorId,
        ));
    }

    public function createProduct(User $user, VendorProductCreateData $data): Product
    {
        $vendorId = $this->vendors->profileForUser($user)->id;

        if (! Item::ownedByVendor($vendorId, $data->itemId)) {
            throw ValidationException::withMessages(['item_id' => 'آیتم انتخاب‌شده متعلق به شما نیست.']);
        }

        return $this->productsService->create(new ProductCreateData(
            itemId: $data->itemId,
            price: $data->price,
            colorId: $data->colorId,
            sizeId: $data->sizeId,
            stock: $data->stock,
            shippingPrice: $data->shippingPrice,
            status: GlobalStatusEnum::ACTIVE,
            images: $data->images,
            vendorId: $vendorId,
        ));
    }

    public function items(User $user, VendorItemFilterData $filters): Paginator
    {
        return Item::filterForVendor($this->vendors->profileForUser($user)->id, $filters);
    }

    public function item(User $user, int $itemId): Item
    {
        return Item::detailsForVendor($this->vendors->profileForUser($user)->id, $itemId);
    }

    public function products(User $user, VendorProductFilterData $filters): Paginator
    {
        return Product::filterForVendor($this->vendors->profileForUser($user)->id, $filters);
    }

    public function product(User $user, int $productId): Product
    {
        return Product::detailsForVendor($this->vendors->profileForUser($user)->id, $productId);
    }

    public function orders(User $user, VendorOrderFilterData $filters): Paginator
    {
        return VendorOrder::filterForVendor($this->vendors->profileForUser($user)->id, $filters);
    }

    public function order(User $user, int $vendorOrderId): VendorOrder
    {
        return VendorOrder::detailsForVendor($this->vendors->profileForUser($user)->id, $vendorOrderId);
    }
}
