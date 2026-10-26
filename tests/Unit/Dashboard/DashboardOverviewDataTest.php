<?php

namespace Tests\Unit\Dashboard;

use App\DTO\Dashboard\DashboardOverviewData;
use Illuminate\Pagination\Paginator;
use PHPUnit\Framework\TestCase;

class DashboardOverviewDataTest extends TestCase
{
    public function test_has_attention_items_is_false_when_all_samples_are_empty(): void
    {
        $overview = new DashboardOverviewData(
            pendingOrders: new Paginator([], 6, 1),
            lowStockProducts: new Paginator([], 5, 1),
            pendingReviews: new Paginator([], 5, 1),
        );

        $this->assertFalse($overview->hasAttentionItems());
    }

    public function test_has_attention_items_is_true_when_any_sample_has_rows(): void
    {
        $overview = new DashboardOverviewData(
            pendingOrders: new Paginator([(object) ['id' => 1]], 6, 1),
            lowStockProducts: new Paginator([], 5, 1),
            pendingReviews: new Paginator([], 5, 1),
        );

        $this->assertTrue($overview->hasAttentionItems());
    }
}
