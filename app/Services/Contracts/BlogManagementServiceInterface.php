<?php

namespace App\Services\Contracts;

use App\DTO\Blog\BlogBannerCreateData;
use App\DTO\Blog\BlogBannerFilterData;
use App\DTO\Blog\BlogBannerUpdateData;
use App\DTO\Blog\BlogCategoryCreateData;
use App\DTO\Blog\BlogCategoryFilterData;
use App\DTO\Blog\BlogCategoryUpdateData;
use App\DTO\Blog\BlogPostCreateData;
use App\DTO\Blog\BlogPostFilterData;
use App\DTO\Blog\BlogPostUpdateData;
use App\Models\BlogBanner;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Contracts\Pagination\Paginator;

interface BlogManagementServiceInterface
{
    public function filterCategories(BlogCategoryFilterData $filters): Paginator;

    public function categoryDetails(int $categoryId): BlogCategory;

    public function parentCategoryOptions(?int $exceptId = null): array;

    public function createCategory(BlogCategoryCreateData $data): BlogCategory;

    public function updateCategory(int $categoryId, BlogCategoryUpdateData $data): BlogCategory;

    public function deleteCategory(int $categoryId): bool;

    public function filterPosts(BlogPostFilterData $filters): Paginator;

    public function postDetails(int $postId): BlogPost;

    public function postCategoryOptions(): array;

    public function createPost(BlogPostCreateData $data): BlogPost;

    public function updatePost(int $postId, BlogPostUpdateData $data): BlogPost;

    public function deletePost(int $postId): void;

    public function filterBanners(BlogBannerFilterData $filters): Paginator;

    public function bannerDetails(int $bannerId): BlogBanner;

    public function createBanner(BlogBannerCreateData $data): BlogBanner;

    public function updateBanner(int $bannerId, BlogBannerUpdateData $data): BlogBanner;

    public function deleteBanner(int $bannerId): void;
}
