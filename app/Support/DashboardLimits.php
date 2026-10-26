<?php

namespace App\Support;

final class DashboardLimits
{
    public const STOCK_THRESHOLD = 5;

    public const PENDING_ORDERS = 6;

    public const LOW_STOCK = 5;

    public const PENDING_REVIEWS = 5;

    public static function stockThreshold(): int
    {
        return self::STOCK_THRESHOLD;
    }

    public static function pendingOrders(): int
    {
        return self::cap(self::PENDING_ORDERS);
    }

    public static function lowStock(): int
    {
        return self::cap(self::LOW_STOCK);
    }

    public static function pendingReviews(): int
    {
        return self::cap(self::PENDING_REVIEWS);
    }

    public static function cap(int $limit, int $max = 8): int
    {
        return max(1, min($max, $limit));
    }
}
