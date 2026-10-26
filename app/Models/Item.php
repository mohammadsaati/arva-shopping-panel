<?php

namespace App\Models;

use App\DTO\Item\ItemCreateData;
use App\DTO\Item\ItemFilterData;
use App\DTO\Item\ItemProductCreateData;
use App\DTO\Item\ItemSelectOptionsData;
use App\DTO\Item\ItemUpdateData;
use App\DTO\Section\SectionItemFilterData;
use App\DTO\Storefront\CatalogFilterData;
use App\DTO\Storefront\ItemSuggestData;
use App\DTO\Storefront\SectionItemsFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\OrderStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection as SupportCollection;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $code
 * @property string|null $image
 * @property string|null $description
 * @property int $category_id
 * @property int|null $brand_id
 * @property GlobalStatusEnum $status
 * @property bool $is_amazing
 * @property Carbon|null $amazing_end_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Product> $products
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductReview> $reviews
 */
#[Guarded('id')]
class Item extends Model
{
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'item_sections')->withTimestamps();
    }

    public function specificationValues(): HasMany
    {
        return $this->hasMany(ItemSpecificationValue::class)->orderBy('sort_order')->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function scopeAvailableForHome(Builder $builder): void
    {
        $builder->where('items.status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('category', fn (Builder $category) => $category->where('status', GlobalStatusEnum::ACTIVE->value))
            ->whereHas('products', fn (Builder $product) => $product
                ->where('status', GlobalStatusEnum::ACTIVE->value)
                ->where('stock', '>', 0));
    }

    public function scopeMatchingSearch(Builder $builder, string $search): void
    {
        $builder->where(function (Builder $builder) use ($search): void {
            $builder->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%")
                ->orWhereHas('brand', function (Builder $brand) use ($search): void {
                    $brand->where('name', 'like', "%{$search}%");
                });

            if (ctype_digit($search)) {
                $builder->orWhere('code', (int) $search);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'status' => GlobalStatusEnum::class,
            'is_amazing' => 'boolean',
            'amazing_end_time' => 'datetime',
        ];
    }

    public static function reviewableById(int $itemId): self
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('category', fn (Builder $category) => $category
                ->where('status', GlobalStatusEnum::ACTIVE->value))
            ->whereHas('products', fn (Builder $product) => $product
                ->where('status', GlobalStatusEnum::ACTIVE->value))
            ->findOrFail($itemId);
    }

    public function hasVerifiedPurchase(int $userId): bool
    {
        return OrderItem::query()
            ->whereHas('order', fn (Builder $order) => $order
                ->where('user_id', $userId)
                ->where('status', OrderStatusEnum::DELIVERED->value))
            ->whereHas('product', fn (Builder $product) => $product->where('item_id', $this->id))
            ->exists();
    }

    /** @return SupportCollection<int, array{specification: Specification, value: string}> */
    public function specificationDisplayRows(): SupportCollection
    {
        return $this->specificationValues
            ->groupBy('specification_id')
            ->map(function ($values): array {
                $specification = $values->first()->specification;

                return [
                    'specification' => $specification,
                    'value' => $values->map(fn (ItemSpecificationValue $value): string => $value->displayValue())->implode('، '),
                ];
            })
            ->sort(fn (array $left, array $right): int => [
                $left['specification']->group->sort_order,
                $left['specification']->group->id,
                $left['specification']->sort_order,
                $left['specification']->id,
            ] <=> [
                $right['specification']->group->sort_order,
                $right['specification']->group->id,
                $right['specification']->sort_order,
                $right['specification']->id,
            ])
            ->values();
    }

    public static function filter(ItemFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'code', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with(['category', 'brand'])
            ->withCount('products')
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $builder->orWhere('code', (int) $search);
                    }
                });
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function details(int $itemId): self
    {
        return self::query()
            ->with(['category', 'brand', 'specificationValues.specification.group', 'specificationValues.option'])
            ->withCount('products')
            ->findOrFail($itemId);
    }

    public static function create(ItemCreateData $data, ?string $imagePath): self
    {
        return DB::transaction(function () use ($data, $imagePath): self {
            $item = new self();
            $item->name = $data->name;
            $item->slug = $data->slug;
            $item->code = $data->code;
            $item->category_id = $data->categoryId;
            $item->brand_id = $data->brandId;
            $item->image = $imagePath;
            $item->description = $data->description;
            $item->status = $data->status;
            $item->is_amazing = $data->isAmazing;
            $item->amazing_end_time = $data->amazingEndTime;
            $item->save();

            ItemSpecificationValue::replaceForItem($item, $data->specifications);

            if ($data->product !== null) {
                Product::createForItem($item, $data->product);
            }

            return $item;
        });
    }

    public function updateDetails(ItemUpdateData $data, ?string $imagePath): self
    {
        return DB::transaction(function () use ($data, $imagePath): self {
            $oldCategoryId = $this->category_id;

            foreach ($data->toModelAttributes() as $attribute => $value) {
                $this->setAttribute($attribute, $value);
            }

            if ($imagePath !== null) {
                $this->image = $imagePath;
            }

            $this->save();

            if ($data->specificationsProvided || $oldCategoryId !== $this->category_id) {
                ItemSpecificationValue::replaceForItem($this, $data->specifications);
            }

            return $this;
        });
    }

    /** @return array{brands: array<int, string>, colors: array<int, string>, sizes: array<int, string>, cities: array<string, array<int, string>>} */
    public static function formOptions(?self $currentItem = null): array
    {
        return [
            'brands' => Brand::activeOptions($currentItem?->brand_id),
            'colors' => Color::options(),
            'sizes' => Size::options(),
            'cities' => City::shippingOptions(),
        ];
    }

    public static function activeOptions(?int $includeItemId = null): array
    {
        return self::query()
            ->where(function (Builder $builder) use ($includeItemId): void {
                $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->when($includeItemId !== null, fn (Builder $builder) => $builder->orWhereKey($includeItemId));
            })
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->mapWithKeys(fn (self $item) => [$item->id => "{$item->name} ({$item->code})"])
            ->all();
    }

    /** @return array<int, string> */
    public static function selectedOption(?int $itemId): array
    {
        if ($itemId === null || $itemId < 1) {
            return [];
        }

        $item = self::query()->find($itemId, ['id', 'name', 'code']);

        return $item === null ? [] : [$item->id => "{$item->name} ({$item->code})"];
    }

    public static function hasActiveOptions(): bool
    {
        return self::query()->where('status', GlobalStatusEnum::ACTIVE->value)->exists();
    }

    /** @return array{results: array<int, array{id: int, text: string}>, pagination: array{more: bool}} */
    public static function selectOptions(ItemSelectOptionsData $filters): array
    {
        $selectedIds = array_values(array_unique(array_filter(
            $filters->selectedIds,
            static fn (int $id): bool => $id > 0,
        )));
        $search = trim($filters->search);

        $selected = $filters->page !== 1 || $selectedIds === []
            ? collect()
            : self::query()->whereKey($selectedIds)->get(['id', 'name', 'code']);

        $matches = self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $builder->orWhereKey((int) $search);
                    }
                });
            })
            ->orderBy('name')
            ->orderBy('id')
            ->simplePaginate(perPage: 25, columns: ['id', 'name', 'code'], page: $filters->page);

        return [
            'results' => $selected
                ->concat($matches->getCollection())
                ->unique('id')
                ->values()
                ->map(fn (self $item): array => [
                    'id' => $item->id,
                    'text' => "{$item->name} ({$item->code})",
                ])
                ->all(),
            'pagination' => ['more' => $matches->hasMorePages()],
        ];
    }

    public static function selectableForSection(SectionItemFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'code'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['name' => 'asc'];

        $query = self::query()
            ->with('category')
            ->withExists(['products as has_available_product' => fn (Builder $products) => $products
                ->where('status', GlobalStatusEnum::ACTIVE->value)
                ->where('stock', '>', 0)])
            ->when(
                $filters->selectedOnly,
                fn (Builder $builder) => $builder->whereKey($filters->selectedItemIds),
                fn (Builder $builder) => $builder->where(function (Builder $builder) use ($filters): void {
                    $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                        ->when($filters->selectedItemIds !== [], fn (Builder $builder) => $builder->orWhereKey($filters->selectedItemIds));
                }),
            )
            ->when($filters->homeAvailable === true, fn (Builder $builder) => $builder->availableForHome())
            ->when($filters->homeAvailable === false, fn (Builder $builder) => $builder->where(function (Builder $builder): void {
                $builder->where('items.status', '!=', GlobalStatusEnum::ACTIVE->value)
                    ->orWhereDoesntHave('category', fn (Builder $category) => $category->where('status', GlobalStatusEnum::ACTIVE->value))
                    ->orWhereDoesntHave('products', fn (Builder $product) => $product
                        ->where('status', GlobalStatusEnum::ACTIVE->value)
                        ->where('stock', '>', 0));
            }))
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $builder->orWhere('code', (int) $search);
                    }
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->orderBy('id')
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function publicSectionItems(SectionItemsFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->where('items.status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('category', fn (Builder $category) => $category->where('status', GlobalStatusEnum::ACTIVE->value))
            ->whereHas('products', fn (Builder $products) => $products->where('status', GlobalStatusEnum::ACTIVE->value))
            ->whereHas('sections', fn (Builder $sections) => $sections
                ->whereKey($filters->sectionId)
                ->where('status', GlobalStatusEnum::ACTIVE->value))
            ->with([
                'category',
                'brand',
                'products' => fn (HasMany $products) => $products
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->orderByDesc('stock')
                    ->orderBy('price')
                    ->limit(1),
            ])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $builder->orWhere('code', (int) $search);
                    }
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->orderBy('id')
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function catalog(CatalogFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'code', 'created_at', 'price'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['created_at' => 'desc'];
        $variantPriceDirection = ($sorts['price'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        $query = self::query()
            ->where('items.status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('category', function (Builder $category) use ($filters): void {
                $category
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->when($filters->categoryId !== null, function (Builder $category) use ($filters): void {
                        $category->where(function (Builder $category) use ($filters): void {
                            $category->whereKey($filters->categoryId)
                                ->orWhere('parent_id', $filters->categoryId);
                        });
                    });
            })
            ->when($filters->brandId !== null, fn (Builder $builder) => $builder->where('brand_id', $filters->brandId))
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $builder->matchingSearch($filters->search);
            })
            ->whereHas('products', function (Builder $products) use ($filters): void {
                $products
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->when($filters->inStockOnly, fn (Builder $products) => $products->where('stock', '>', 0))
                    ->when($filters->minPrice !== null, fn (Builder $products) => $products->where('price', '>=', $filters->minPrice))
                    ->when($filters->maxPrice !== null, fn (Builder $products) => $products->where('price', '<=', $filters->maxPrice));
            })
            ->with([
                'category',
                'brand',
                'products' => function (HasMany $products) use ($filters, $variantPriceDirection): void {
                    $products->where('status', GlobalStatusEnum::ACTIVE->value);

                    if ($filters->inStockOnly) {
                        $products->where('stock', '>', 0);
                    }

                    if ($filters->minPrice !== null) {
                        $products->where('price', '>=', $filters->minPrice);
                    }

                    if ($filters->maxPrice !== null) {
                        $products->where('price', '<=', $filters->maxPrice);
                    }

                    $products
                        ->with(['color', 'size'])
                        ->orderBy('price', $variantPriceDirection)
                        ->orderBy('id')
                        ->limit(1);
                },
            ]);

        foreach ($sorts as $column => $direction) {
            if ($column === 'price') {
                $aggregate = $direction === 'desc' ? 'withMax' : 'withMin';
                $query->{$aggregate}([
                    'products as catalog_price' => function (Builder $products) use ($filters): void {
                        $products
                            ->where('status', GlobalStatusEnum::ACTIVE->value)
                            ->when($filters->inStockOnly, fn (Builder $products) => $products->where('stock', '>', 0))
                            ->when($filters->minPrice !== null, fn (Builder $products) => $products->where('price', '>=', $filters->minPrice))
                            ->when($filters->maxPrice !== null, fn (Builder $products) => $products->where('price', '<=', $filters->maxPrice));
                    },
                ], 'price')->orderBy('catalog_price', $direction);

                continue;
            }

            $query->orderBy('items.'.$column, $direction);
        }

        return $query
            ->orderBy('items.id')
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    /** @return Collection<int, self> */
    public static function suggestForSearch(ItemSuggestData $data): Collection
    {
        if (! $data->shouldQuery()) {
            return new Collection;
        }

        return self::query()
            ->availableForHome()
            ->matchingSearch($data->normalizedTerm())
            ->with([
                'category',
                'brand',
                'products' => function (HasMany $products): void {
                    $products
                        ->where('status', GlobalStatusEnum::ACTIVE->value)
                        ->where('stock', '>', 0)
                        ->orderBy('price')
                        ->orderBy('id')
                        ->limit(1);
                },
            ])
            ->orderBy('name')
            ->orderBy('id')
            ->limit($data->cappedLimit())
            ->get();
    }

    public static function isActiveForSection(int $itemId): bool
    {
        return self::query()
            ->whereKey($itemId)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->exists();
    }

    /** @return Collection<int, self> */
    public static function amazingForHome(int $limit = 8): Collection
    {
        return self::query()
            ->availableForHome()
            ->where('is_amazing', true)
            ->where('amazing_end_time', '>', now())
            ->with([
                'category',
                'brand',
                'products' => fn ($products) => $products
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->where('stock', '>', 0)
                    ->orderBy('price')
                    ->limit(1),
            ])
            ->orderBy('amazing_end_time')
            ->limit(max(1, min($limit, 12)))
            ->get();
    }

    /** @return Collection<int, Product> */
    public static function relatedProducts(int $categoryId, int $excludedItemId, int $limit = 8): Collection
    {
        $items = self::query()
            ->availableForHome()
            ->where('category_id', $categoryId)
            ->whereKeyNot($excludedItemId)
            ->with([
                'category',
                'brand',
                'products' => fn (HasMany $products) => $products
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->where('stock', '>', 0)
                    ->with(['color', 'size'])
                    ->orderBy('price')
                    ->orderBy('id')
                    ->limit(1),
            ])
            ->orderByDesc('created_at')
            ->limit(max(1, min($limit, 12)))
            ->get();

        $relatedProducts = new Collection();

        foreach ($items as $item) {
            $product = $item->products->first();

            if ($product !== null) {
                $product->setRelation('item', $item);
                $relatedProducts->push($product);
            }
        }

        return $relatedProducts;
    }
}
