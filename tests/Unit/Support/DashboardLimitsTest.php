<?php

namespace Tests\Unit\Support;

use App\Support\DashboardLimits;
use PHPUnit\Framework\TestCase;

class DashboardLimitsTest extends TestCase
{
    public function test_stock_threshold_stays_small(): void
    {
        $this->assertSame(5, DashboardLimits::stockThreshold());
    }

    public function test_sample_limits_stay_within_cap(): void
    {
        $this->assertSame(6, DashboardLimits::pendingOrders());
        $this->assertSame(5, DashboardLimits::lowStock());
        $this->assertSame(5, DashboardLimits::pendingReviews());
    }

    public function test_cap_keeps_values_between_one_and_max(): void
    {
        $this->assertSame(1, DashboardLimits::cap(0));
        $this->assertSame(1, DashboardLimits::cap(-3));
        $this->assertSame(4, DashboardLimits::cap(4));
        $this->assertSame(8, DashboardLimits::cap(40));
        $this->assertSame(3, DashboardLimits::cap(9, 3));
    }
}
