<?php

namespace App\Services\Contracts;

use App\DTO\VendorProduct\VendorProductAdminFilterData;
use App\DTO\VendorProduct\VendorProductDecisionData;
use App\Models\VendorProduct;
use Illuminate\Contracts\Pagination\Paginator;

interface VendorProductApprovalServiceInterface
{
    public function filterForAdmin(VendorProductAdminFilterData $filters): Paginator;

    public function detailsForAdmin(int $vendorProductId): VendorProduct;

    public function decideForAdmin(int $vendorProductId, VendorProductDecisionData $data): VendorProduct;

    public function pendingCount(): int;
}
