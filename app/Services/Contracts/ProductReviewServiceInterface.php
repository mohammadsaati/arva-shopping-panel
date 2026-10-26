<?php

namespace App\Services\Contracts;

use App\DTO\ProductReview\ProductReviewCreateData;
use App\DTO\ProductReview\ProductReviewFilterData;
use App\DTO\ProductReview\ProductReviewPublicFilterData;
use App\DTO\ProductReview\ProductReviewUpdateData;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

interface ProductReviewServiceInterface
{
    public function submit(int $itemId, ?User $user, ProductReviewCreateData $data): ProductReview;

    public function publicReviews(ProductReviewPublicFilterData $filters): Paginator;

    /** @return array{count: int, average: float, distribution: array<int, int>} */
    public function publicSummary(int $itemId): array;

    public function filterForAdmin(ProductReviewFilterData $filters): Paginator;

    public function detailsForAdmin(int $reviewId): ProductReview;

    public function updateForAdmin(int $reviewId, int $adminId, ProductReviewUpdateData $data): ProductReview;

    public function pendingCount(): int;
}
