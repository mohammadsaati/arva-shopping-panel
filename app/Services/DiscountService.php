<?php

namespace App\Services;

use App\DTO\Discount\DiscountCreateData;
use App\DTO\Discount\DiscountFilterData;
use App\DTO\Discount\DiscountQuoteData;
use App\DTO\Discount\DiscountQuoteRequestData;
use App\DTO\Discount\DiscountTargetFilterData;
use App\DTO\Discount\DiscountUpdateData;
use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\User;
use App\Services\Contracts\DiscountServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

class DiscountService implements DiscountServiceInterface
{
    public function create(DiscountCreateData $data): Discount
    {
        return Discount::create($data);
    }

    public function update(int $discountId, DiscountUpdateData $data): Discount
    {
        return Discount::details($discountId)->updateDetails($data);
    }

    public function details(int $discountId): Discount
    {
        return Discount::details($discountId);
    }

    public function filter(DiscountFilterData $filters): Paginator
    {
        return Discount::filter($filters);
    }

    public function selectableTargets(DiscountTargetFilterData $filters): Paginator
    {
        return match ($filters->targetType) {
            'products' => Product::filterForDiscountSelection($filters),
            'categories' => Category::filterForDiscountSelection($filters),
            'users' => User::filterForDiscountSelection($filters),
            default => throw new \InvalidArgumentException('Unsupported discount target type.'),
        };
    }

    public function targetExists(string $targetType, int $targetId): bool
    {
        return match ($targetType) {
            'products' => Product::existsForDiscountSelection($targetId),
            'categories' => Category::existsForDiscountSelection($targetId),
            'users' => User::existsForDiscountSelection($targetId),
            default => false,
        };
    }

    public function quoteForCart(DiscountQuoteRequestData $data, ShoppingCart $cart, ?User $user = null, bool $lock = false): DiscountQuoteData
    {
        $discount = Discount::findByCode($data->code, $lock);

        if ($discount === null || $discount->status !== GlobalStatusEnum::ACTIVE) {
            $this->reject('کد تخفیف معتبر یا فعال نیست.');
        }

        if ($discount->user_id !== null
            && ($user === null || $discount->user_id !== $user->id)) {
            $this->reject('این کد تخفیف به حساب کاربری شما اختصاص ندارد.');
        }

        $now = now();

        if (($discount->starts_at !== null && $now->lessThan($discount->starts_at))
            || ($discount->ends_at !== null && $now->greaterThan($discount->ends_at))) {
            $this->reject('کد تخفیف در بازه اعتبار خود نیست.');
        }

        if ($discount->usage_limit !== null && $discount->usage_count >= $discount->usage_limit) {
            $this->reject('ظرفیت استفاده از این کد تخفیف تکمیل شده است.');
        }

        if ($user !== null
            && $discount->usage_limit_per_user !== null
            && Discount::usageCountForUser($discount->id, $user->id) >= $discount->usage_limit_per_user) {
            $this->reject('شما به سقف استفاده از این کد تخفیف رسیده‌اید.');
        }

        $subtotal = $cart->subtotal();

        if ($discount->min_order_amount !== null && $subtotal < $discount->min_order_amount) {
            $this->reject('حداقل مبلغ کالاها برای استفاده از این کد تخفیف رعایت نشده است.');
        }

        $productIds = $discount->products->modelKeys();
        $categoryIds = $discount->categories->modelKeys();
        $hasRestrictions = $productIds !== [] || $categoryIds !== [];
        $eligibleSubtotal = 0;

        foreach ($cart->discountItems() as $cartItem) {
            $product = $cartItem->product;
            $categoryId = (int) $product->item->category_id;
            $isEligible = ! $hasRestrictions
                || in_array($product->id, $productIds, true)
                || in_array($categoryId, $categoryIds, true);

            if ($isEligible) {
                $eligibleSubtotal += $product->price * $cartItem->count;
            }
        }

        if ($eligibleSubtotal === 0) {
            $this->reject('کالاهای سبد خرید شامل این کد تخفیف نمی‌شوند.');
        }

        $discountAmount = match ($discount->type) {
            DiscountTypeEnum::FIXED => min($discount->amount, $eligibleSubtotal),
            DiscountTypeEnum::PERCENTAGE => intdiv($eligibleSubtotal, 100) * $discount->amount
                + intdiv(($eligibleSubtotal % 100) * $discount->amount, 100),
        };

        if ($discount->max_discount_amount !== null) {
            $discountAmount = min($discountAmount, $discount->max_discount_amount);
        }

        if ($discountAmount < 1) {
            $this->reject('مبلغ تخفیف برای این سبد خرید قابل محاسبه نیست.');
        }

        return new DiscountQuoteData(
            discount: $discount,
            discountAmount: $discountAmount,
            eligibleSubtotal: $eligibleSubtotal,
        );
    }

    private function reject(string $message): never
    {
        throw ValidationException::withMessages(['discount_code' => $message]);
    }
}
