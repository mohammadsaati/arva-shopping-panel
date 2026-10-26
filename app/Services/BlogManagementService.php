<?php

namespace App\Services;

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
use App\Services\Contracts\BlogManagementServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class BlogManagementService implements BlogManagementServiceInterface
{
    public function filterCategories(BlogCategoryFilterData $filters): Paginator
    {
        return BlogCategory::filter($filters);
    }

    public function categoryDetails(int $categoryId): BlogCategory
    {
        return BlogCategory::details($categoryId);
    }

    public function parentCategoryOptions(?int $exceptId = null): array
    {
        return BlogCategory::parentOptions($exceptId);
    }

    public function createCategory(BlogCategoryCreateData $data): BlogCategory
    {
        $imagePath = $this->storeOptionalImage($data->image, 'blog/categories');

        try {
            return BlogCategory::create($data, $imagePath);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function updateCategory(int $categoryId, BlogCategoryUpdateData $data): BlogCategory
    {
        $category = BlogCategory::details($categoryId);
        $oldImagePath = $category->image;
        $newImagePath = $this->storeOptionalImage($data->image, 'blog/categories');

        try {
            $category->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if (($newImagePath !== null || $data->removeImage) && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return BlogCategory::details($categoryId);
    }

    public function deleteCategory(int $categoryId): bool
    {
        $category = BlogCategory::details($categoryId);
        if ($category->hasPosts()) {
            return false;
        }

        $imagePath = $category->image;
        $category->deleteRecord();

        if ($imagePath !== null) {
            Storage::disk('public')->delete($imagePath);
        }

        return true;
    }

    public function filterPosts(BlogPostFilterData $filters): Paginator
    {
        return BlogPost::filter($filters);
    }

    public function postDetails(int $postId): BlogPost
    {
        return BlogPost::details($postId);
    }

    public function postCategoryOptions(): array
    {
        return BlogPost::categoryOptions();
    }

    public function createPost(BlogPostCreateData $data): BlogPost
    {
        $imagePath = $this->storeOptionalImage($data->featuredImage, 'blog/posts');

        try {
            return BlogPost::create($data, $imagePath);
        } catch (Throwable $exception) {
            if ($imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }
    }

    public function updatePost(int $postId, BlogPostUpdateData $data): BlogPost
    {
        $post = BlogPost::details($postId);
        $oldImagePath = $post->featured_image;
        $newImagePath = $this->storeOptionalImage($data->featuredImage, 'blog/posts');

        try {
            $post->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if (($newImagePath !== null || $data->removeFeaturedImage) && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return BlogPost::details($postId);
    }

    public function deletePost(int $postId): void
    {
        $post = BlogPost::details($postId);
        $imagePath = $post->featured_image;
        $post->deleteRecord();

        if ($imagePath !== null) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    public function filterBanners(BlogBannerFilterData $filters): Paginator
    {
        return BlogBanner::filter($filters);
    }

    public function bannerDetails(int $bannerId): BlogBanner
    {
        return BlogBanner::details($bannerId);
    }

    public function createBanner(BlogBannerCreateData $data): BlogBanner
    {
        $imagePath = $this->storeRequiredImage($data->image, 'blog/banners');
        $mobileImagePath = null;

        try {
            $mobileImagePath = $this->storeOptionalImage($data->mobileImage, 'blog/banners/mobile');

            return BlogBanner::create($data, $imagePath, $mobileImagePath);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete(array_filter([$imagePath, $mobileImagePath]));
            throw $exception;
        }
    }

    public function updateBanner(int $bannerId, BlogBannerUpdateData $data): BlogBanner
    {
        $banner = BlogBanner::details($bannerId);
        $oldImagePath = $banner->image;
        $oldMobileImagePath = $banner->mobile_image;
        $imagePath = null;
        $mobileImagePath = null;

        try {
            $imagePath = $this->storeOptionalImage($data->image, 'blog/banners');
            $mobileImagePath = $this->storeOptionalImage($data->mobileImage, 'blog/banners/mobile');
            $banner->updateDetails($data, $imagePath, $mobileImagePath);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete(array_filter([$imagePath, $mobileImagePath]));
            throw $exception;
        }

        if ($imagePath !== null && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        if (($mobileImagePath !== null || $data->removeMobileImage) && $oldMobileImagePath !== null) {
            Storage::disk('public')->delete($oldMobileImagePath);
        }

        return BlogBanner::details($bannerId);
    }

    public function deleteBanner(int $bannerId): void
    {
        $banner = BlogBanner::details($bannerId);
        $imagePaths = array_filter([$banner->image, $banner->mobile_image]);
        $banner->deleteRecord();

        if ($imagePaths !== []) {
            Storage::disk('public')->delete($imagePaths);
        }
    }

    private function storeRequiredImage(UploadedFile $image, string $directory): string
    {
        $path = $this->storeOptionalImage($image, $directory);
        if ($path === null) {
            throw new RuntimeException('The blog image could not be saved.');
        }

        return $path;
    }

    private function storeOptionalImage(?UploadedFile $image, string $directory): ?string
    {
        if ($image === null) {
            return null;
        }

        $path = $image->store($directory, 'public');
        if (! is_string($path)) {
            throw new RuntimeException('The blog image could not be saved.');
        }

        return $path;
    }
}
