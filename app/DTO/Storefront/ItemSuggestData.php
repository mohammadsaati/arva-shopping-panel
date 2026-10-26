<?php

namespace App\DTO\Storefront;

final readonly class ItemSuggestData
{
    public const MIN_TERM_LENGTH = 2;

    public function __construct(
        public string $term,
        public int $limit = 8,
    ) {}

    public function normalizedTerm(): string
    {
        return trim($this->term);
    }

    public function shouldQuery(): bool
    {
        return mb_strlen($this->normalizedTerm()) >= self::MIN_TERM_LENGTH;
    }

    public function cappedLimit(): int
    {
        return max(1, min(12, $this->limit));
    }
}
