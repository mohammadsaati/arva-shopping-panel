<?php

namespace App\Services\Contracts;

use App\DTO\VendorPanel\VendorItemCreateData;
use App\DTO\VendorPanel\VendorItemFilterData;
use App\DTO\VendorPanel\VendorOrderFilterData;
use App\DTO\VendorPanel\VendorProductCreateData;
use App\DTO\VendorPanel\VendorProductFilterData;
use App\Models\Item;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorOrder;
use Illuminate\Contracts\Pagination\Paginator;

interface VendorPanelServiceInterface
{
    /** @return array{items: int, products: int, orders: int} */
    public function dashboard(User $user): array;

    /** @return array{categories: array<int, string>, brands: array<int, string>} */
    public function itemFormOptions(): array;

    /** @return array{items: array<int, string>, colors: array<int, string>, sizes: array<int, string>} */
    public function productFormOptions(User $user): array;

    public function createItem(User $user, VendorItemCreateData $data): Item;

    public function createProduct(User $user, VendorProductCreateData $data): Product;

    public function items(User $user, VendorItemFilterData $filters): Paginator;

    public function item(User $user, int $itemId): Item;

    public function products(User $user, VendorProductFilterData $filters): Paginator;

    public function product(User $user, int $productId): Product;

    public function orders(User $user, VendorOrderFilterData $filters): Paginator;

    public function order(User $user, int $vendorOrderId): VendorOrder;
}
