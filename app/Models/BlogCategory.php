<?php

namespace App\Models;

use App\DTO\Blog\BlogCategoryCreateData;
use App\DTO\Blog\BlogCategoryFilterData;
use App\DTO\Blog\BlogCategoryUpdateData;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int|null $parent_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property int $priority
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read BlogCategory|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BlogCategory> $children
 * @property-read \Illuminate\Database\Eloquent\Collection<int, BlogPost> $posts
 */
#[Guarded('id')]
class BlogCategory extends Model
{
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'blog_category_id');
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    public static function filter(BlogCategoryFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'slug', 'priority', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with('parent')
            ->withCount(['children', 'posts'])
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

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function details(int $categoryId): self
    {
        return self::query()->with(['parent', 'children'])->withCount(['children', 'posts'])->findOrFail($categoryId);
    }

    /** @return Collection<int, self> */
    public static function forBlogHome(): Collection
    {
        return self::query()
            ->whereNull('parent_id')
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->orderBy('priority')
            ->orderBy('id')
            ->limit(6)
            ->get();
    }

    public static function publicBySlug(string $slug): ?self
    {
        return self::query()
            ->with('parent')
            ->where('slug', $slug)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where(function (Builder $query): void {
                $query->whereNull('parent_id')
                    ->orWhereHas('parent', fn (Builder $parent) => $parent->where('status', GlobalStatusEnum::ACTIVE->value));
            })
            ->first();
    }

    public function hasPosts(): bool
    {
        return $this->posts()->exists();
    }

    /** @return array<int, string> */
    public static function parentOptions(?int $exceptId = null): array
    {
        return self::query()
            ->whereNull('parent_id')
            ->when($exceptId !== null, fn (Builder $builder) => $builder->whereKeyNot($exceptId))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function create(BlogCategoryCreateData $data, ?string $imagePath): self
    {
        $category = new self();
        $category->name = $data->name;
        $category->slug = $data->slug;
        $category->parent_id = $data->parentId;
        $category->description = $data->description;
        $category->image = $imagePath;
        $category->priority = $data->priority;
        $category->status = $data->status;
        $category->save();

        return $category;
    }

    public function updateDetails(BlogCategoryUpdateData $data, ?string $imagePath): self
    {
        foreach ($data->toModelAttributes() as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        if ($imagePath !== null) {
            $this->image = $imagePath;
        } elseif ($data->removeImage) {
            $this->image = null;
        }

        $this->save();

        return $this;
    }

    public function deleteRecord(): void
    {
        $this->delete();
    }
}
