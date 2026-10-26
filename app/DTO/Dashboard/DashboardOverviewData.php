<?php

namespace App\DTO\Dashboard;

use Illuminate\Contracts\Pagination\Paginator;

final readonly class DashboardOverviewData
{
    /**
     * @param Paginator<\App\Models\Order> $pendingOrders
     * @param Paginator<\App\Models\Product> $lowStockProducts
     * @param Paginator<\App\Models\ProductReview> $pendingReviews
     */
    public function __construct(
        public Paginator $pendingOrders,
        public Paginator $lowStockProducts,
        public Paginator $pendingReviews,
    ) {
    }

    public function hasAttentionItems(): bool
    {
        return $this->pendingOrders->isNotEmpty()
            || $this->lowStockProducts->isNotEmpty()
            || $this->pendingReviews->isNotEmpty();
    }
}
