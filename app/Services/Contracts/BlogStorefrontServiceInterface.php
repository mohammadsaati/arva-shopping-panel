<?php

namespace App\Services\Contracts;

use App\DTO\Blog\BlogCategoryPostsFilterData;
use App\Models\BlogBanner;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

interface BlogStorefrontServiceInterface
{
    /** @return Collection<int, BlogCategory> */
    public function homeCategories(): Collection;

    public function categoryBySlug(string $slug): ?BlogCategory;

    /** @return Collection<int, BlogPost> */
    public function featuredPosts(?int $categoryId): Collection;

    /** @return Collection<int, BlogPost> */
    public function latestPosts(?int $categoryId): Collection;

    /** @return Collection<int, BlogPost> */
    public function searchPosts(string $search, ?int $categoryId = null): Collection;

    public function publishedCount(?int $categoryId = null): int;

    /** @return Collection<int, BlogPost> */
    public function homeLatestPosts(): Collection;

    /** @return Collection<int, BlogBanner> */
    public function homeBanners(): Collection;

    /** @return Collection<int, Item> */
    public function amazingItems(): Collection;

    public function categoryPosts(BlogCategoryPostsFilterData $filters): Paginator;

    public function postBySlug(string $slug): BlogPost;

    /** @return Collection<int, BlogPost> */
    public function relatedPosts(BlogPost $post): Collection;

    public function previousPost(BlogPost $post): ?BlogPost;

    public function nextPost(BlogPost $post): ?BlogPost;
}
