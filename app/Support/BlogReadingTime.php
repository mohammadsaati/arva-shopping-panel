<?php

namespace App\Support;

final class BlogReadingTime
{
    public const WORDS_PER_MINUTE = 180;

    public static function minutes(?string $body, ?string $excerpt = null): int
    {
        $text = trim(strip_tags(($body ?? '').' '.($excerpt ?? '')));

        if ($text === '') {
            return 1;
        }

        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) ceil(count($words !== false ? $words : []) / self::WORDS_PER_MINUTE));
    }
}
