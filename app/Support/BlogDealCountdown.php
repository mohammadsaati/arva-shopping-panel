<?php

namespace App\Support;

use DateTimeImmutable;
use DateTimeInterface;

final class BlogDealCountdown
{
    /**
     * @return array{days: int, hours: int, minutes: int, seconds: int}
     */
    public static function units(?DateTimeInterface $endTime, ?DateTimeInterface $now = null): array
    {
        $nowTimestamp = ($now ?? new DateTimeImmutable)->getTimestamp();
        $secondsLeft = $endTime === null
            ? 0
            : max(0, $endTime->getTimestamp() - $nowTimestamp);

        return [
            'days' => intdiv($secondsLeft, 86400),
            'hours' => intdiv($secondsLeft % 86400, 3600),
            'minutes' => intdiv($secondsLeft % 3600, 60),
            'seconds' => $secondsLeft % 60,
        ];
    }

    public static function isExpired(?DateTimeInterface $endTime, ?DateTimeInterface $now = null): bool
    {
        if ($endTime === null) {
            return true;
        }

        return $endTime->getTimestamp() <= ($now ?? new DateTimeImmutable)->getTimestamp();
    }
}
