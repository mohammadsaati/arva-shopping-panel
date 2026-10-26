<?php

namespace App\Services;

use App\DTO\Blog\BlogCategoryPostsFilterData;
use App\Models\BlogBanner;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Item;
use App\Services\Contracts\BlogStorefrontServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

class BlogStorefrontService implements BlogStorefrontServiceInterface
{
    public function homeCategories(): Collection
    {
        return BlogCategory::forBlogHome();
    }

    public function categoryBySlug(string $slug): ?BlogCategory
    {
        return BlogCategory::publicBySlug($slug);
    }

    public function featuredPosts(?int $categoryId): Collection
    {
        return BlogPost::featuredForBlogHome($categoryId);
    }

    public function latestPosts(?int $categoryId): Collection
    {
        return BlogPost::latestForBlogHome($categoryId);
    }

    public function searchPosts(string $search, ?int $categoryId = null): Collection
    {
        return BlogPost::searchPublic($search, $categoryId);
    }

    public function publishedCount(?int $categoryId = null): int
    {
        return BlogPost::publicCount($categoryId);
    }

    public function homeLatestPosts(): Collection
    {
        return BlogPost::latestForStoreHome();
    }

    public function homeBanners(): Collection
    {
        return BlogBanner::forBlogHome();
    }

    public function amazingItems(): Collection
    {
        return Item::amazingForHome(5);
    }

    public function categoryPosts(BlogCategoryPostsFilterData $filters): Paginator
    {
        return BlogPost::forPublicCategory($filters);
    }

    public function postBySlug(string $slug): BlogPost
    {
        $post = BlogPost::publicBySlug($slug);
        $post->recordPublicView();

        return $post;
    }

    public function relatedPosts(BlogPost $post): Collection
    {
        return BlogPost::relatedForBlogPost($post);
    }

    public function previousPost(BlogPost $post): ?BlogPost
    {
        return BlogPost::previousPublicPost($post);
    }

    public function nextPost(BlogPost $post): ?BlogPost
    {
        return BlogPost::nextPublicPost($post);
    }
}
