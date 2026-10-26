<?php

namespace App\Models;

use App\DTO\Blog\BlogPostCreateData;
use App\DTO\Blog\BlogCategoryPostsFilterData;
use App\DTO\Blog\BlogPostFilterData;
use App\DTO\Blog\BlogPostUpdateData;
use App\Enums\BlogPostStatusEnum;
use App\Enums\GlobalStatusEnum;
use App\Support\BlogReadingTime;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $blog_category_id
 * @property int|null $author_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string|null $body
 * @property string|null $featured_image
 * @property BlogPostStatusEnum $status
 * @property bool $is_featured
 * @property int $featured_order
 * @property int $views_count
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property Carbon|null $published_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read BlogCategory $category
 * @property-read Admin|null $author
 */
#[Guarded('id')]
class BlogPost extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function scopeVisibleOnBlog(Builder $query): Builder
    {
        return $query
            ->where('status', BlogPostStatusEnum::PUBLISHED->value)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereHas('category', function (Builder $category): void {
                $category->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->where(function (Builder $visibleParent): void {
                        $visibleParent->whereNull('parent_id')
                            ->orWhereHas('parent', fn (Builder $parent) => $parent->where('status', GlobalStatusEnum::ACTIVE->value));
                    });
            });
    }

    public function scopeInBlogCategory(Builder $query, ?int $categoryId): Builder
    {
        return $query->when($categoryId !== null, function (Builder $query) use ($categoryId): void {
            $query->whereHas('category', function (Builder $category) use ($categoryId): void {
                $category->where(function (Builder $match) use ($categoryId): void {
                    $match->whereKey($categoryId)->orWhere('parent_id', $categoryId);
                });
            });
        });
    }

    protected function casts(): array
    {
        return [
            'status' => BlogPostStatusEnum::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function publishedAtInputValue(): ?string
    {
        return $this->published_at?->format('Y-m-d\\TH:i');
    }

    public function readingMinutes(): int
    {
        return BlogReadingTime::minutes($this->body, $this->excerpt);
    }

    public static function filter(BlogPostFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'title', 'status', 'is_featured', 'published_at', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with(['category', 'author'])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($filters->blogCategoryId !== null, fn (Builder $builder) => $builder->where('blog_category_id', $filters->blogCategoryId))
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function details(int $postId): self
    {
        return self::query()->with(['category', 'author'])->findOrFail($postId);
    }

    /** @return Collection<int, self> */
    public static function featuredForBlogHome(?int $categoryId): Collection
    {
        return self::query()
            ->visibleOnBlog()
            ->inBlogCategory($categoryId)
            ->with('category')
            ->where('is_featured', true)
            ->orderBy('featured_order')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();
    }

    /** @return Collection<int, self> */
    public static function latestForBlogHome(?int $categoryId): Collection
    {
        return self::query()
            ->visibleOnBlog()
            ->inBlogCategory($categoryId)
            ->with('category')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();
    }

    /** @return Collection<int, self> */
    public static function latestForStoreHome(): Collection
    {
        return self::query()
            ->visibleOnBlog()
            ->with('category')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();
    }

    public static function publicBySlug(string $slug): self
    {
        return self::query()
            ->visibleOnBlog()
            ->with(['category.parent', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    public static function forPublicCategory(BlogCategoryPostsFilterData $filters): Paginator
    {
        $allowedSorts = ['published_at', 'views_count'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['published_at' => 'desc'];

        $query = self::query()
            ->visibleOnBlog()
            ->inBlogCategory($filters->categoryId)
            ->with('category')
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $builder) use ($filters): void {
                $search = trim($filters->search);
                $builder->where(function (Builder $match) use ($search): void {
                    $match->where('title', 'like', "%{$search}%")
                        ->orWhere('excerpt', 'like', "%{$search}%");
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->orderByDesc('id')
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    /** @return Collection<int, self> */
    public static function relatedForBlogPost(self $post): Collection
    {
        return self::query()
            ->visibleOnBlog()
            ->inBlogCategory($post->category->parent_id ?: $post->blog_category_id)
            ->with('category')
            ->whereKeyNot($post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
    }

    /** @return Collection<int, self> */
    public static function searchPublic(string $search, ?int $categoryId = null, int $limit = 12): Collection
    {
        $term = trim($search);

        return self::query()
            ->visibleOnBlog()
            ->inBlogCategory($categoryId)
            ->with('category')
            ->when($term !== '', function (Builder $builder) use ($term): void {
                $builder->where(function (Builder $match) use ($term): void {
                    $match->where('title', 'like', "%{$term}%")
                        ->orWhere('excerpt', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public static function publicCount(?int $categoryId = null): int
    {
        return self::query()
            ->visibleOnBlog()
            ->inBlogCategory($categoryId)
            ->count();
    }

    public static function previousPublicPost(self $post): ?self
    {
        return self::query()
            ->visibleOnBlog()
            ->with('category')
            ->where(function (Builder $query) use ($post): void {
                $query->where('published_at', '<', $post->published_at)
                    ->orWhere(function (Builder $sameTime) use ($post): void {
                        $sameTime->where('published_at', $post->published_at)
                            ->where('id', '<', $post->id);
                    });
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();
    }

    public static function nextPublicPost(self $post): ?self
    {
        return self::query()
            ->visibleOnBlog()
            ->with('category')
            ->where(function (Builder $query) use ($post): void {
                $query->where('published_at', '>', $post->published_at)
                    ->orWhere(function (Builder $sameTime) use ($post): void {
                        $sameTime->where('published_at', $post->published_at)
                            ->where('id', '>', $post->id);
                    });
            })
            ->orderBy('published_at')
            ->orderBy('id')
            ->first();
    }

    public function recordPublicView(): void
    {
        $this->increment('views_count');
    }

    /** @return array<int, string> */
    public static function categoryOptions(): array
    {
        return BlogCategory::query()->orderBy('name')->pluck('name', 'id')->all();
    }

    public static function create(BlogPostCreateData $data, ?string $imagePath): self
    {
        $post = new self();
        $post->blog_category_id = $data->blogCategoryId;
        $post->author_id = $data->authorId;
        $post->title = $data->title;
        $post->slug = $data->slug;
        $post->excerpt = $data->excerpt;
        $post->body = $data->body;
        $post->featured_image = $imagePath;
        $post->status = $data->status;
        $post->is_featured = $data->isFeatured;
        $post->featured_order = $data->featuredOrder;
        $post->meta_title = $data->metaTitle;
        $post->meta_description = $data->metaDescription;
        $post->published_at = $data->publishedAt ?? ($data->status === BlogPostStatusEnum::PUBLISHED ? now() : null);
        $post->save();

        return $post;
    }

    public function deleteRecord(): void
    {
        $this->delete();
    }

    public function updateDetails(BlogPostUpdateData $data, ?string $imagePath): self
    {
        foreach ($data->toModelAttributes() as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        if ($imagePath !== null) {
            $this->featured_image = $imagePath;
        } elseif ($data->removeFeaturedImage) {
            $this->featured_image = null;
        }

        if ($this->status === BlogPostStatusEnum::PUBLISHED && $this->published_at === null) {
            $this->published_at = now();
        }

        $this->save();

        return $this;
    }
}
