<?php

namespace App\Models;

use App\DTO\Slider\SliderCreateData;
use App\DTO\Slider\SliderFilterData;
use App\DTO\Slider\SliderUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;
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
 * @property string $title
 * @property int $priority
 * @property string $image
 * @property int|null $product_id
 * @property int|null $category_id
 * @property int|null $section_id
 * @property string|null $link
 * @property SliderTypeEnum $type
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Slider extends Model
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

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    protected function casts(): array
    {
        return [
            'type' => SliderTypeEnum::class,
            'status' => GlobalStatusEnum::class,
        ];
    }

    public static function filter(SliderFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'title', 'priority', 'type', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['priority' => 'asc'];

        $query = self::query()
            ->with(['product.item', 'category', 'section'])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('title', 'like', "%{$search}%")
                        ->orWhere('link', 'like', "%{$search}%")
                        ->orWhereHas('category', fn (Builder $categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('product.item', fn (Builder $itemQuery) => $itemQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('section', fn (Builder $sectionQuery) => $sectionQuery->where('name', 'like', "%{$search}%"));

                    if (ctype_digit($search)) {
                        $builder->orWhereKey((int) $search);
                    }
                });
            })
            ->when($filters->type !== null, fn (Builder $builder) => $builder->where('type', $filters->type->value))
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function details(int $sliderId): self
    {
        return self::query()->with(['product.item', 'category', 'section'])->findOrFail($sliderId);
    }

    /**
     * @param array<int, int> $visibleSectionIds
     * @return Collection<int, self>
     */
    public static function forHome(array $visibleSectionIds, int $limit = 6): Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where(function (Builder $builder) use ($visibleSectionIds): void {
                $builder->where(function (Builder $builder): void {
                    $builder->where('type', SliderTypeEnum::LINK->value)->whereNotNull('link');
                })->orWhere(function (Builder $builder): void {
                    $builder->where('type', SliderTypeEnum::PRODUCT->value)
                        ->whereHas('product', fn (Builder $product) => $product
                            ->where('status', GlobalStatusEnum::ACTIVE->value)
                            ->where('stock', '>', 0)
                            ->whereHas('item', fn (Builder $item) => $item->availableForHome()));
                })->orWhere(function (Builder $builder): void {
                    $builder->where('type', SliderTypeEnum::CATEGORY->value)
                        ->whereHas('category', fn (Builder $category) => $category->where('status', GlobalStatusEnum::ACTIVE->value));
                })->orWhere(function (Builder $builder) use ($visibleSectionIds): void {
                    $builder->where('type', SliderTypeEnum::SESSION->value)
                        ->whereIn('section_id', $visibleSectionIds)
                        ->whereHas('section', fn (Builder $section) => $section->where('status', GlobalStatusEnum::ACTIVE->value));
                });
            })
            ->with(['product.item', 'category', 'section'])
            ->orderBy('priority')
            ->orderBy('id')
            ->limit(max(1, min($limit, 12)))
            ->get();
    }

    public function homeUrl(): ?string
    {
        return match ($this->type) {
            SliderTypeEnum::PRODUCT => $this->product?->item
                ? route('shop.products.show', ['productId' => $this->product->id])
                : null,
            SliderTypeEnum::CATEGORY => $this->category ? route('shop.index', ['category' => $this->category->id]) : null,
            SliderTypeEnum::SESSION => $this->section ? route('home').'#section-'.$this->section->id : null,
            SliderTypeEnum::LINK => $this->link && in_array(parse_url($this->link, PHP_URL_SCHEME), ['http', 'https'], true)
                ? $this->link
                : null,
        };
    }

    public static function create(SliderCreateData $data, string $imagePath): self
    {
        $slider = new self();
        $slider->title = $data->title;
        $slider->priority = $data->priority;
        $slider->image = $imagePath;
        $slider->product_id = $data->productId;
        $slider->category_id = $data->categoryId;
        $slider->section_id = $data->sectionId;
        $slider->link = $data->link;
        $slider->type = $data->type;
        $slider->status = $data->status;
        $slider->save();

        return $slider;
    }

    public function updateDetails(SliderUpdateData $data, ?string $imagePath): self
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
