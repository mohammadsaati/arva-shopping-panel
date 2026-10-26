<?php

namespace App\Enums;

enum ProductReviewPointSentimentEnum: string
{
    use LabelTrait, ValuesTrait;

    case POSITIVE = 'positive';
    case NEGATIVE = 'negative';

    protected function labelKey(): string
    {
        return 'product_review_point_sentiment';
    }

    public function color(): string
    {
        return match ($this) {
            self::POSITIVE => 'success',
            self::NEGATIVE => 'danger',
        };
    }
}
