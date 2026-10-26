<?php

namespace App\Services\Contracts;

use App\DTO\Discount\DiscountCreateData;
use App\DTO\Discount\DiscountFilterData;
use App\DTO\Discount\DiscountQuoteData;
use App\DTO\Discount\DiscountQuoteRequestData;
use App\DTO\Discount\DiscountTargetFilterData;
use App\DTO\Discount\DiscountUpdateData;
use App\Models\Discount;
use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

interface DiscountServiceInterface
{
    public function create(DiscountCreateData $data): Discount;

    public function update(int $discountId, DiscountUpdateData $data): Discount;

    public function details(int $discountId): Discount;

    public function filter(DiscountFilterData $filters): Paginator;

    public function selectableTargets(DiscountTargetFilterData $filters): Paginator;

    public function targetExists(string $targetType, int $targetId): bool;

    public function quoteForCart(DiscountQuoteRequestData $data, ShoppingCart $cart, ?User $user = null, bool $lock = false): DiscountQuoteData;
}
