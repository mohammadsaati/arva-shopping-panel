<?php

namespace App\Models;

use App\DTO\Category\CategoryCreateData;
use App\DTO\Category\CategoryFilterData;
use App\DTO\Category\CategorySelectOptionsData;
use App\DTO\Category\CategoryUpdateData;
use App\DTO\Discount\DiscountTargetFilterData;
use App\DTO\Specification\SpecificationCategoryFilterData;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property string $slug
 * @property int $priority
 * @property string|null $icon
 * @property string|null $image
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Category extends Model
{
    use HasFactory;

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function specificationGroups(): HasMany
    {
        return $this->hasMany(SpecificationGroup::class);
    }

    public function specifications(): HasManyThrough
    {
        return $this->hasManyThrough(Specification::class, SpecificationGroup::class);
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    /** @return array<int, string> */
    public static function activeOptions(?int $exceptCategoryId = null, ?int $includeCategoryId = null): array
    {
        return self::query()
            ->where(function (Builder $builder) use ($includeCategoryId): void {
                $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->when($includeCategoryId !== null, fn (Builder $builder) => $builder->orWhereKey($includeCategoryId));
            })
            ->when($exceptCategoryId !== null, fn (Builder $builder) => $builder->whereKeyNot($exceptCategoryId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /** @return array{results: array<int, array{id: int, text: string}>, pagination: array{more: bool}} */
    public static function selectOptions(CategorySelectOptionsData $filters): array
    {
        $selectedIds = array_values(array_unique(array_filter(
            array_map('intval', $filters->selectedIds),
            static fn (int $id): bool => $id > 0,
        )));
        $search = trim($filters->search);

        $selected = $filters->page !== 1 || $selectedIds === []
            ? collect()
            : self::query()->whereKey($selectedIds)->get(['id', 'name']);

        $matches = self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $builder->orWhereKey((int) $search);
                    }
                });
            })
            ->orderBy('name')
            ->orderBy('id')
            ->simplePaginate(perPage: 25, columns: ['id', 'name'], page: $filters->page);

        return [
            'results' => $selected
                ->concat($matches->getCollection())
                ->unique('id')
                ->values()
                ->map(fn (self $category): array => [
                    'id' => $category->id,
                    'text' => $category->name,
                ])
                ->all(),
            'pagination' => ['more' => $matches->hasMorePages()],
        ];
    }

    public static function filter(CategoryFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'priority', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with('parent')
            ->withCount(['children', 'items'])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
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

    public static function specificationCategories(SpecificationCategoryFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['name' => 'asc'];

        $query = self::query()
            ->with('parent')
            ->withCount(['specificationGroups', 'specifications'])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $builder->where(function (Builder $builder) use ($filters): void {
                    $builder->where('name', 'like', "%{$filters->search}%")
                        ->orWhere('slug', 'like', "%{$filters->search}%");
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->orderBy('id')
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    /** @return Collection<int, self> */
    public static function homePageCategories(int $limit = 6): Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('items', function (Builder $builder): void {
                $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->whereHas('products', function (Builder $productBuilder): void {
                        $productBuilder->where('status', GlobalStatusEnum::ACTIVE->value)
                            ->where('stock', '>', 0);
                    });
            })
            ->orderBy('priority')
            ->orderBy('name')
            ->limit(max(1, min($limit, 12)))
            ->get();
    }

    /** @return Collection<int, self> */
    public static function activeNavigationTree(): Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->whereNull('parent_id')
            ->with(['children' => function (HasMany $builder): void {
                $builder
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->orderBy('priority')
                    ->orderBy('name');
            }])
            ->orderBy('priority')
            ->orderBy('name')
            ->get();
    }

    public static function details(int $categoryId): self
    {
        return self::query()
            ->where('id', $categoryId)
            ->with(['parent', 'children'])
            ->withCount(['children', 'items', 'specificationGroups', 'specifications'])
            ->firstOrFail();
    }

    public static function publicDetailsBySlug(string $slug): self
    {
        return self::query()
            ->where('slug', $slug)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->with([
                'parent' => fn (BelongsTo $parent) => $parent->where('status', GlobalStatusEnum::ACTIVE->value),
                'children' => fn (HasMany $children) => $children
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->orderBy('priority')
                    ->orderBy('name'),
            ])
            ->firstOrFail();
    }

    public static function create(CategoryCreateData $data, ?string $imagePath): self
    {
        $category = new self();
        $category->name = $data->name;
        $category->parent_id = $data->parentId;
        $category->slug = $data->slug;
        $category->priority = $data->priority;
        $category->icon = $data->icon;
        $category->image = $imagePath;
        $category->status = $data->status;
        $category->save();

        return $category;
    }

    public function updateDetails(CategoryUpdateData $data, ?string $imagePath): self
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

    /** @return array{parents: array<int, string>} */
    public static function formOptions(?int $excludeCategoryId = null, ?int $includeCategoryId = null): array
    {
        return ['parents' => self::activeOptions($excludeCategoryId, $includeCategoryId)];
    }

    public static function filterForDiscountSelection(DiscountTargetFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['name' => 'asc'];

        $query = self::query()
            ->with('parent')
            ->withCount('items')
            ->when($filters->selectedOnly, fn (Builder $builder) => $builder->whereKey($filters->selectedIds))
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $builder) use ($filters): void {
                $search = trim($filters->search);
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('parent', fn (Builder $parent) => $parent->where('name', 'like', "%{$search}%"));

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

    public static function existsForDiscountSelection(int $categoryId): bool
    {
        return self::query()->whereKey($categoryId)->exists();
    }
}
