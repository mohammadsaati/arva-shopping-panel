<?php

namespace App\DTO\ProductReview;

final readonly class ProductReviewCreateData
{
    /**
     * @param list<string> $positivePoints
     * @param list<string> $negativePoints
     */
    public function __construct(
        public int $rating,
        public ?string $title,
        public string $body,
        public ?string $guestName,
        public ?string $guestEmail,
        public array $positivePoints = [],
        public array $negativePoints = [],
    ) {}
}
