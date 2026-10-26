<?php

namespace App\Models;

use App\DTO\Banner\BannerCreateData;
use App\DTO\Banner\BannerFilterData;
use App\DTO\Banner\BannerUpdateData;
use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $image
 * @property BannerTypeEnum $type
 * @property int|null $product_id
 * @property int|null $category_id
 * @property string|null $link
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Banner extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    protected function casts(): array
    {
        return [
            'type' => BannerTypeEnum::class,
            'status' => GlobalStatusEnum::class,
        ];
    }

    public static function filter(BannerFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'type', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with(['product.item', 'category'])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    if (ctype_digit($search)) {
                        $builder->whereKey((int) $search);
                    }

                    $builder->orWhere('link', 'like', "%{$search}%")
                        ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('product.item', fn (Builder $itemQuery) => $itemQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters->type !== null, fn (Builder $builder) => $builder->where('type', $filters->type->value))
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function details(int $bannerId): self
    {
        return self::query()->with(['product.item', 'category'])->findOrFail($bannerId);
    }

    /** @return Collection<int, self> */
    public static function forHome(int $limit = 4): Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where(function (Builder $builder): void {
                $builder->where(function (Builder $builder): void {
                    $builder->where('type', BannerTypeEnum::LINK->value)->whereNotNull('link');
                })->orWhere(function (Builder $builder): void {
                    $builder->where('type', BannerTypeEnum::PRODUCT->value)
                        ->whereHas('product', fn (Builder $product) => $product
                            ->where('status', GlobalStatusEnum::ACTIVE->value)
                            ->where('stock', '>', 0)
                            ->whereHas('item', fn (Builder $item) => $item->availableForHome()));
                })->orWhere(function (Builder $builder): void {
                    $builder->where('type', BannerTypeEnum::CATEGORY->value)
                        ->whereHas('category', fn (Builder $category) => $category->where('status', GlobalStatusEnum::ACTIVE->value));
                });
            })
            ->with(['product.item', 'category'])
            ->orderByDesc('id')
            ->limit(max(1, min($limit, 8)))
            ->get();
    }

    public function homeUrl(): ?string
    {
        return match ($this->type) {
            BannerTypeEnum::PRODUCT => $this->product?->item
                ? route('shop.products.show', ['productId' => $this->product->id])
                : null,
            BannerTypeEnum::CATEGORY => $this->category ? route('shop.index', ['category' => $this->category->id]) : null,
            BannerTypeEnum::LINK => $this->link && in_array(parse_url($this->link, PHP_URL_SCHEME), ['http', 'https'], true)
                ? $this->link
                : null,
        };
    }

    public static function create(BannerCreateData $data, string $imagePath): self
    {
        $banner = new self();
        $banner->image = $imagePath;
        $banner->type = $data->type;
        $banner->product_id = $data->productId;
        $banner->category_id = $data->categoryId;
        $banner->link = $data->link;
        $banner->status = $data->status;
        $banner->save();

        return $banner;
    }

    public function updateDetails(BannerUpdateData $data, ?string $imagePath): self
    {
        foreach ($data->toModelAttributes() as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        if ($imagePath !== null) {
            $this->image = $imagePath;
        }

        $this->save();

        return $this;
    }
}
