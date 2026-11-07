<?php

namespace App\Services;

use App\DTO\VendorProduct\VendorProductAdminFilterData;
use App\DTO\VendorProduct\VendorProductDecisionData;
use App\Enums\MarketplaceApprovalStatusEnum;
use App\Models\VendorProduct;
use App\Services\Contracts\VendorProductApprovalServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

class VendorProductApprovalService implements VendorProductApprovalServiceInterface
{
    public function filterForAdmin(VendorProductAdminFilterData $filters): Paginator
    {
        return VendorProduct::filterForAdmin($filters);
    }

    public function detailsForAdmin(int $vendorProductId): VendorProduct
    {
        return VendorProduct::detailsForAdmin($vendorProductId);
    }

    public function decideForAdmin(int $vendorProductId, VendorProductDecisionData $data): VendorProduct
    {
        if ($data->approvalStatus === MarketplaceApprovalStatusEnum::PENDING) {
            throw ValidationException::withMessages(['approval_status' => 'وضعیت محصول باید تأیید یا رد باشد.']);
        }

        if ($data->approvalStatus === MarketplaceApprovalStatusEnum::REJECTED && trim((string) $data->rejectionReason) === '') {
            throw ValidationException::withMessages(['rejection_reason' => 'برای رد محصول، دلیل را وارد کنید.']);
        }

        VendorProduct::runInTransaction(function () use ($vendorProductId, $data): void {
            $vendorProduct = VendorProduct::detailsForAdmin($vendorProductId);

            if ($data->approvalStatus === MarketplaceApprovalStatusEnum::APPROVED) {
                $vendorProduct->approveByAdmin();

                return;
            }

            $vendorProduct->rejectByAdmin(trim((string) $data->rejectionReason));
        });

        return VendorProduct::detailsForAdmin($vendorProductId);
    }

    public function pendingCount(): int
    {
        return VendorProduct::pendingCount();
    }
}
