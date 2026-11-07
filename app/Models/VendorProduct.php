<?php

namespace App\Models;

use App\DTO\VendorProduct\VendorProductAdminFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\MarketplaceApprovalStatusEnum;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $product_id
 * @property int $vendor_id
 * @property int|null $price
 * @property int $stock
 * @property int $shipping_price
 * @property GlobalStatusEnum $status
 * @property MarketplaceApprovalStatusEnum $approval_status
 * @property Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Product $product
 * @property-read Vendor $vendor
 */
#[Guarded('id')]
class VendorProduct extends Model
{
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function scopeApprovedForStorefront(Builder $builder): void
    {
        $builder->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where('approval_status', MarketplaceApprovalStatusEnum::APPROVED->value)
            ->whereHas('vendor', fn (Builder $vendor) => $vendor
                ->where('status', GlobalStatusEnum::ACTIVE->value)
                ->where('approval_status', MarketplaceApprovalStatusEnum::APPROVED->value));
    }

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'shipping_price' => 'integer',
            'status' => GlobalStatusEnum::class,
            'approval_status' => MarketplaceApprovalStatusEnum::class,
            'approved_at' => 'datetime',
        ];
    }

    /** @return Collection<int, self> */
    public static function availableForProduct(int $productId): Collection
    {
        return self::availableQuery($productId)
            ->with('vendor.commission')
            ->orderByRaw('COALESCE(price, (SELECT price FROM products WHERE products.id = vendor_products.product_id)) ASC')
            ->orderBy('id')
            ->get();
    }

    public static function filterForAdmin(VendorProductAdminFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'price', 'stock', 'approval_status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];
        $search = trim((string) $filters->search);

        $query = self::query()
            ->with(['product.item', 'product.color', 'product.size', 'product.images', 'vendor.user'])
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('approval_status', $filters->status->value))
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->whereHas('product.item', fn (Builder $item) => $item->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('vendor', function (Builder $vendor) use ($search): void {
                            $vendor->where('company_name', 'like', "%{$search}%")
                                ->orWhere('store_name', 'like', "%{$search}%")
                                ->orWhereHas('user', fn (Builder $user) => $user->where('phone', 'like', "%{$search}%"));
                        });

                    if (ctype_digit($search)) {
                        $builder->orWhereKey((int) $search);
                    }
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function detailsForAdmin(int $vendorProductId): self
    {
        return self::query()
            ->with(['product.item.category', 'product.item.brand', 'product.color', 'product.size', 'product.images', 'vendor.user'])
            ->findOrFail($vendorProductId);
    }

    public static function pendingCount(): int
    {
        return self::query()->where('approval_status', MarketplaceApprovalStatusEnum::PENDING->value)->count();
    }

    public static function runInTransaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }

    public static function selectedForCart(int $productId, ?int $offerId, bool $lock = false): ?self
    {
        $query = self::availableQuery($productId)
            ->with('vendor.commission')
            ->when($offerId !== null, fn (Builder $builder) => $builder->whereKey($offerId));

        if ($offerId === null) {
            $query->orderByRaw('COALESCE(price, (SELECT price FROM products WHERE products.id = vendor_products.product_id)) ASC')
                ->orderBy('id');
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public static function findForCart(int $productId, int $offerId, bool $lock = false): ?self
    {
        return self::selectedForCart($productId, $offerId, $lock);
    }

    public function effectivePrice(Product $product): int
    {
        return $this->price ?? $product->price;
    }

    public static function createForProduct(Product $product, int $vendorId): self
    {
        $offer = new self();
        $offer->product_id = $product->id;
        $offer->vendor_id = $vendorId;
        $offer->price = $product->price;
        $offer->stock = $product->stock;
        $offer->shipping_price = $product->shipping_price;
        $offer->status = GlobalStatusEnum::ACTIVE;
        $offer->approval_status = MarketplaceApprovalStatusEnum::PENDING;
        $offer->approved_at = null;
        $offer->save();

        return $offer;
    }

    public function approveByAdmin(): void
    {
        $this->approval_status = MarketplaceApprovalStatusEnum::APPROVED;
        $this->approved_at = now();
        $this->rejection_reason = null;
        $this->save();
    }

    public function rejectByAdmin(string $reason): void
    {
        $this->approval_status = MarketplaceApprovalStatusEnum::REJECTED;
        $this->approved_at = null;
        $this->rejection_reason = $reason;
        $this->save();
    }

    public function decreaseStock(int $quantity): void
    {
        if ($quantity < 1 || $quantity > $this->stock) {
            throw ValidationException::withMessages(['cart' => 'موجودی فروشنده برای ثبت سفارش کافی نیست.']);
        }

        $this->stock -= $quantity;
        $this->save();
    }

    public function increaseStock(int $quantity): void
    {
        if ($quantity > 0) {
            $this->stock += $quantity;
            $this->save();
        }
    }

    private static function availableQuery(int $productId): Builder
    {
        return self::query()
            ->where('product_id', $productId)
            ->approvedForStorefront()
            ->where('stock', '>', 0);
    }
}
