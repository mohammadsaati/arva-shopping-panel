<?php

namespace Tests\Unit\Support;

use App\Support\BlogDealCountdown;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class BlogDealCountdownTest extends TestCase
{
    public function test_missing_end_time_is_expired_and_zeroed(): void
    {
        $this->assertTrue(BlogDealCountdown::isExpired(null));
        $this->assertSame(
            ['days' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0],
            BlogDealCountdown::units(null),
        );
    }

    public function test_past_end_time_is_expired(): void
    {
        $now = new DateTimeImmutable('2026-10-25 12:00:00');
        $end = new DateTimeImmutable('2026-10-25 11:59:59');

        $this->assertTrue(BlogDealCountdown::isExpired($end, $now));
        $this->assertSame(
            ['days' => 0, 'hours' => 0, 'minutes' => 0, 'seconds' => 0],
            BlogDealCountdown::units($end, $now),
        );
    }

    public function test_splits_remaining_time_into_units(): void
    {
        $now = new DateTimeImmutable('2026-10-25 12:00:00');
        $end = new DateTimeImmutable('2026-10-27 15:04:08');

        $this->assertFalse(BlogDealCountdown::isExpired($end, $now));
        $this->assertSame(
            ['days' => 2, 'hours' => 3, 'minutes' => 4, 'seconds' => 8],
            BlogDealCountdown::units($end, $now),
        );
    }
}
