<?php

namespace App\DTO\ProductReview;

use App\Enums\ProductReviewApprovalStatusEnum;

final readonly class ProductReviewUpdateData
{
    /**
     * @param list<string> $positivePoints
     * @param list<string> $negativePoints
     */
    public function __construct(
        public ProductReviewApprovalStatusEnum $approvalStatus,
        public array $positivePoints,
        public array $negativePoints,
    ) {}
}
