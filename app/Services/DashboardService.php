<?php

namespace App\Services;

use App\DTO\Dashboard\DashboardOverviewData;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Services\Contracts\DashboardServiceInterface;
use App\Support\DashboardLimits;

class DashboardService implements DashboardServiceInterface
{
    public function overview(): DashboardOverviewData
    {
        return new DashboardOverviewData(
            pendingOrders: Order::dashboardPending(DashboardLimits::pendingOrders()),
            lowStockProducts: Product::dashboardLowStock(
                DashboardLimits::stockThreshold(),
                DashboardLimits::lowStock(),
            ),
            pendingReviews: ProductReview::dashboardPending(DashboardLimits::pendingReviews()),
        );
    }
}
