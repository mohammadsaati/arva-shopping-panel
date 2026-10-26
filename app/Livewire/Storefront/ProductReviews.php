<?php

namespace App\Livewire\Storefront;

use App\DTO\ProductReview\ProductReviewPublicFilterData;
use App\Services\Contracts\ProductReviewServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class ProductReviews extends Component
{
    use WithPagination;

    #[Locked]
    public int $itemId;

    protected ProductReviewServiceInterface $reviewsService;

    public function boot(ProductReviewServiceInterface $reviewsService): void
    {
        $this->reviewsService = $reviewsService;
    }

    public function mount(int $itemId): void
    {
        $this->itemId = $itemId;
    }

    public function render(): View
    {
        return view('livewire.storefront.product-reviews', [
            'reviews' => $this->reviewsService->publicReviews(new ProductReviewPublicFilterData(
                itemId: $this->itemId,
                page: $this->getPage(),
            )),
            'summary' => $this->reviewsService->publicSummary($this->itemId),
        ]);
    }
}
