<?php

namespace App\Services;

use App\DTO\ProductReview\ProductReviewCreateData;
use App\DTO\ProductReview\ProductReviewFilterData;
use App\DTO\ProductReview\ProductReviewPublicFilterData;
use App\DTO\ProductReview\ProductReviewUpdateData;
use App\Enums\ProductReviewApprovalStatusEnum;
use App\Models\Item;
use App\Models\ProductReview;
use App\Models\User;
use App\Services\Contracts\ProductReviewServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;

class ProductReviewService implements ProductReviewServiceInterface
{
    public function submit(int $itemId, ?User $user, ProductReviewCreateData $data): ProductReview
    {
        $item = Item::reviewableById($itemId);
        $verifiedPurchase = $user !== null && $item->hasVerifiedPurchase($user->id);

        return ProductReview::createFromStorefront(
            $item,
            $user,
            $data,
            ProductReviewApprovalStatusEnum::PENDING,
            $verifiedPurchase,
        );
    }

    public function publicReviews(ProductReviewPublicFilterData $filters): Paginator
    {
        return ProductReview::publicForItem($filters);
    }

    public function publicSummary(int $itemId): array
    {
        return ProductReview::publicSummaryForItem($itemId);
    }

    public function filterForAdmin(ProductReviewFilterData $filters): Paginator
    {
        return ProductReview::filterForAdmin($filters);
    }

    public function detailsForAdmin(int $reviewId): ProductReview
    {
        return ProductReview::detailsForAdmin($reviewId);
    }

    public function updateForAdmin(int $reviewId, int $adminId, ProductReviewUpdateData $data): ProductReview
    {
        ProductReview::runInTransaction(function () use ($reviewId, $adminId, $data): void {
            $review = ProductReview::detailsForAdmin($reviewId);

            switch ($data->approvalStatus) {
                case ProductReviewApprovalStatusEnum::APPROVED:
                    $review->approveByAdmin($adminId);
                    break;
                case ProductReviewApprovalStatusEnum::REJECTED:
                    $review->rejectByAdmin($adminId);
                    break;
                case ProductReviewApprovalStatusEnum::PENDING:
                    $review->returnToPending();
                    break;
            }

            $review->replacePointsByAdmin($data->positivePoints, $data->negativePoints);
        });

        return ProductReview::detailsForAdmin($reviewId);
    }

    public function pendingCount(): int
    {
        return ProductReview::pendingCount();
    }
}
