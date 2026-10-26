<?php

namespace App\Http\Controllers\Admin;

use App\DTO\ProductReview\ProductReviewUpdateData;
use App\Enums\ProductReviewApprovalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminProductReviewRequest;
use App\Services\Contracts\ProductReviewServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function __construct(private readonly ProductReviewServiceInterface $reviews) {}

    public function index(): View
    {
        return view('admin.product-reviews.index');
    }

    public function show(int $reviewId): View
    {
        return view('admin.product-reviews.show', [
            'review' => $this->reviews->detailsForAdmin($reviewId),
            'statusOptions' => collect(ProductReviewApprovalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($option) => [$option->value => $option->key])
                ->all(),
        ]);
    }

    public function update(UpdateAdminProductReviewRequest $request, int $reviewId): RedirectResponse
    {
        $this->reviews->updateForAdmin(
            $reviewId,
            (int) $request->user('admin')->id,
            new ProductReviewUpdateData(
                approvalStatus: $request->enum('approval_status', ProductReviewApprovalStatusEnum::class),
                positivePoints: $request->positivePoints(),
                negativePoints: $request->negativePoints(),
            ),
        );

        return redirect()
            ->route('admin.product-reviews.show', $reviewId)
            ->with('success', 'نظر و نکات آن با موفقیت به‌روزرسانی شد.');
    }
}
