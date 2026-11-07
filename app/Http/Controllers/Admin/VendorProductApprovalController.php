<?php

namespace App\Http\Controllers\Admin;

use App\DTO\VendorProduct\VendorProductDecisionData;
use App\Enums\MarketplaceApprovalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminVendorProductApprovalRequest;
use App\Services\Contracts\VendorProductApprovalServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class VendorProductApprovalController extends Controller
{
    public function __construct(private readonly VendorProductApprovalServiceInterface $vendorProducts)
    {
    }

    public function index(): View
    {
        return view('admin.vendor-products.index');
    }

    public function show(int $vendorProductId): View
    {
        $vendorProduct = $this->vendorProducts->detailsForAdmin($vendorProductId);
        $approvalOptions = collect(MarketplaceApprovalStatusEnum::toKeyValueType())
            ->reject(fn ($option) => $option->value === MarketplaceApprovalStatusEnum::PENDING->value)
            ->mapWithKeys(fn ($option) => [$option->value => $option->key])
            ->all();

        return view('admin.vendor-products.show', [
            'vendorProduct' => $vendorProduct,
            'approvalOptions' => $approvalOptions,
            'selectedApprovalStatus' => $vendorProduct->approval_status === MarketplaceApprovalStatusEnum::PENDING
                ? null
                : $vendorProduct->approval_status->value,
        ]);
    }

    public function updateApproval(UpdateAdminVendorProductApprovalRequest $request, int $vendorProductId): RedirectResponse
    {
        $vendorProduct = $this->vendorProducts->decideForAdmin($vendorProductId, new VendorProductDecisionData(
            approvalStatus: $request->approvalStatus(),
            rejectionReason: $request->rejectionReason(),
        ));

        $message = $vendorProduct->approval_status === MarketplaceApprovalStatusEnum::APPROVED
            ? 'محصول فروشنده تأیید شد.'
            : 'محصول فروشنده با ثبت دلیل رد شد.';

        return redirect()
            ->route('admin.vendor-products.show', $vendorProductId)
            ->with('success', $message);
    }
}
