<?php

namespace App\Models;

use App\DTO\Item\ItemProductCreateData;
use App\DTO\Product\ProductCreateData;
use App\DTO\Product\ProductFilterData;
use App\DTO\Product\ProductUpdateData;
use App\DTO\Discount\DiscountTargetFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\ProductStockFilterEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $item_id
 * @property int $price
 * @property int|null $color_id
 * @property int|null $size_id
 * @property int $stock
 * @property int $shipping_price
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, ProductImage> $images
 * @property-read Collection<int, ProductCity> $cityRates
 */

#[Guarded('id')]
class Product extends Model
{
    use HasFactory;

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(ShoppingCartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('id');
    }

    public function cityRates(): HasMany
    {
        return $this->hasMany(ProductCity::class)->orderBy('city_id');
    }

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'shipping_price' => 'integer',
            'status' => GlobalStatusEnum::class,
        ];
    }

    public static function filter(ProductFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'item_id', 'price', 'stock', 'shipping_price', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with(['item', 'color', 'size'])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->whereHas('item', function (Builder $itemQuery) use ($search): void {
                    $itemQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $itemQuery->orWhere('code', (int) $search);
                    }
                });
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value))
            ->when($filters->itemId !== null, fn (Builder $builder) => $builder->where('item_id', $filters->itemId))
            ->when($filters->colorId !== null, fn (Builder $builder) => $builder->where('color_id', $filters->colorId))
            ->when($filters->sizeId !== null, fn (Builder $builder) => $builder->where('size_id', $filters->sizeId))
            ->when($filters->stockFilter !== null, function (Builder $builder) use ($filters): void {
                $threshold = $filters->resolvedLowStockThreshold();

                match ($filters->stockFilter) {
                    ProductStockFilterEnum::IN_STOCK => $builder->where('stock', '>', 0),
                    ProductStockFilterEnum::LOW_STOCK => $builder
                        ->where('stock', '>', 0)
                        ->where('stock', '<=', $threshold),
                    ProductStockFilterEnum::OUT_OF_STOCK => $builder->where('stock', '=', 0),
                };
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    /** @return Collection<int, self> */
    public static function featuredForHome(int $limit = 8, ?string $search = null): Collection
    {
        $search = trim((string) $search);

        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where('stock', '>', 0)
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->whereHas('item', function (Builder $itemQuery) use ($search): void {
                    $itemQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $itemQuery->orWhere('code', (int) $search);
                    }
                });
            })
            ->whereHas('item', function (Builder $builder): void {
                $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->whereHas('category', fn (Builder $categoryBuilder) => $categoryBuilder->where('status', GlobalStatusEnum::ACTIVE->value));
            })
            ->with(['item.category', 'item.brand'])
            ->orderByDesc('created_at')
            ->limit(max(1, min($limit, 16)))
            ->get();
    }

    public static function dashboardLowStock(int $threshold, int $limit): Paginator
    {
        return self::dashboardActiveQuery()
            ->where('stock', '<=', $threshold)
            ->with('item')
            ->orderBy('stock')
            ->orderBy('id')
            ->simplePaginate(perPage: $limit, page: 1)
            ->withQueryString();
    }

    /** @param array<int, string> $imagePaths */
    public static function create(ProductCreateData $data, array $imagePaths = []): self
    {
        return DB::transaction(function () use ($data, $imagePaths): self {
            $product = new self();
            $product->item_id = $data->itemId;
            $product->price = $data->price;
            $product->color_id = $data->colorId;
            $product->size_id = $data->sizeId;
            $product->stock = $data->stock;
            $product->shipping_price = $data->shippingPrice;
            $product->status = $data->status;
            $product->save();

            foreach ($imagePaths as $path) {
                ProductImage::createForProduct($product, $path);
            }

            ProductCity::replaceForProduct($product, $data->cityRates);

            return $product;
        });
    }

    public static function details(int $productId): self
    {
        return self::query()
            ->with(['item', 'color', 'size', 'images', 'cityRates.city'])
            ->findOrFail($productId);
    }

    public static function findPurchasableForCart(int $productId, bool $lock = false): ?self
    {
        $query = self::query()
            ->whereKey($productId)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where('stock', '>', 0)
            ->whereHas('item', fn (Builder $item) => $item
                ->where('status', GlobalStatusEnum::ACTIVE->value)
                ->whereHas('category', fn (Builder $category) => $category
                    ->where('status', GlobalStatusEnum::ACTIVE->value)))
            ->with(['item', 'color', 'size']);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public function decreaseStock(int $quantity): void
    {
        if ($quantity < 1 || $quantity > $this->stock) {
            throw ValidationException::withMessages([
                'cart' => 'موجودی یکی از کالاها برای ثبت سفارش کافی نیست.',
            ]);
        }

        $this->stock -= $quantity;
        $this->save();
    }

    public function increaseStock(int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        $this->stock += $quantity;
        $this->save();
    }

    public static function storefrontDetails(int $productId): self
    {
        return self::query()
            ->whereKey($productId)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('item', fn (Builder $item) => $item
                ->where('status', GlobalStatusEnum::ACTIVE->value)
                ->whereHas('category', fn (Builder $category) => $category
                    ->where('status', GlobalStatusEnum::ACTIVE->value)))
            ->with([
                'item.category',
                'item.brand',
                'item.specificationValues.specification.group',
                'item.specificationValues.option',
                'item.products' => fn (HasMany $products) => $products
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->with(['color', 'size'])
                    ->orderBy('price')
                    ->orderBy('id'),
                'color',
                'size',
                'images',
            ])
            ->firstOrFail();
    }

    /**
     * @param array<int, string> $newImagePaths
     * @param array<int, string> $replacementPaths Image ID to new path.
     * @return array<int, string> Paths no longer referenced after commit.
     */
    public function updateDetails(ProductUpdateData $data, array $newImagePaths = [], array $replacementPaths = []): array
    {
        return DB::transaction(function () use ($data, $newImagePaths, $replacementPaths): array {
            $attributes = $data->toModelAttributes();

            if ($attributes !== []) {
                foreach ($attributes as $attribute => $value) {
                    $this->setAttribute($attribute, $value);
                }

                $this->save();
            }

            $oldPaths = [];

            foreach ($replacementPaths as $imageId => $path) {
                $oldPaths[] = ProductImage::findForProduct($this->id, $imageId)->replaceImage($path);
            }

            foreach ($data->deleteImageIds as $imageId) {
                $oldPaths[] = ProductImage::findForProduct($this->id, $imageId)->removeImage();
            }

            foreach ($newImagePaths as $path) {
                ProductImage::createForProduct($this, $path);
            }

            if ($data->cityRatesProvided) {
                ProductCity::replaceForProduct($this, $data->cityRates);
            }

            return $oldPaths;
        });
    }

    public static function createForItem(Item $item, ItemProductCreateData $data): self
    {
        $product = new self();
        $product->item_id = $item->id;
        $product->price = $data->price;
        $product->color_id = $data->colorId;
        $product->size_id = $data->sizeId;
        $product->stock = $data->stock;
        $product->shipping_price = $data->shippingPrice;
        $product->status = $data->status;
        $product->save();

        ProductCity::replaceForProduct($product, $data->cityRates);

        return $product;
    }

    /** @return array{items: array<int, string>, hasItems: bool, colors: array<int, string>, sizes: array<int, string>, cities: array<string, array<int, string>>} */
    public static function formOptions(?int $includeItemId = null): array
    {
        return [
            'items' => Item::selectedOption($includeItemId),
            'hasItems' => Item::hasActiveOptions(),
            'colors' => Color::options(),
            'sizes' => Size::options(),
            'cities' => City::shippingOptions(),
        ];
    }

    /** @return array<int, string> */
    public static function itemOptions(?int $selectedItemId = null): array
    {
        return Item::selectedOption($selectedItemId);
    }

    /** @return array<int, string> */
    public static function colorFilterOptions(): array
    {
        return Color::options();
    }

    /** @return array<int, string> */
    public static function sizeFilterOptions(): array
    {
        return Size::options();
    }

    /** @return array<int, string> */
    public static function adminOptions(?int $includeProductId = null): array
    {
        return self::query()
            ->with(['item', 'color', 'size'])
            ->where(function (Builder $builder) use ($includeProductId): void {
                $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->when($includeProductId !== null, fn (Builder $builder) => $builder->orWhereKey($includeProductId));
            })
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(function (self $product): array {
                $attributes = array_filter([
                    $product->color?->name,
                    $product->size?->value,
                    'موجودی ' . $product->stock,
                ]);

                return [$product->id => $product->item->name . ' — ' . implode(' / ', $attributes) . ' (#' . $product->id . ')'];
            })
            ->all();
    }

    public static function filterForDiscountSelection(DiscountTargetFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'price', 'stock', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with(['item', 'color', 'size'])
            ->when($filters->selectedOnly, fn (Builder $builder) => $builder->whereKey($filters->selectedIds))
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $builder) use ($filters): void {
                $search = trim($filters->search);
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->whereHas('item', function (Builder $item) use ($search): void {
                        $item->where('name', 'like', "%{$search}%");

                        if (ctype_digit($search)) {
                            $item->orWhere('code', (int) $search);
                        }
                    })
                        ->orWhereHas('color', fn (Builder $color) => $color->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('size', fn (Builder $size) => $size->where('value', 'like', "%{$search}%"));

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

    public static function existsForDiscountSelection(int $productId): bool
    {
        return self::query()->whereKey($productId)->exists();
    }

    private static function dashboardActiveQuery(): Builder
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('item', fn (Builder $item) => $item
                ->where('status', GlobalStatusEnum::ACTIVE->value)
                ->whereHas('category', fn (Builder $category) => $category->where('status', GlobalStatusEnum::ACTIVE->value)));
    }
}
