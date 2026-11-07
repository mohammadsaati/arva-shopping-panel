<?php

namespace App\DTO\VendorProduct;

use App\Enums\MarketplaceApprovalStatusEnum;

final readonly class VendorProductDecisionData
{
    public function __construct(
        public MarketplaceApprovalStatusEnum $approvalStatus,
        public ?string $rejectionReason,
    ) {
    }
}
