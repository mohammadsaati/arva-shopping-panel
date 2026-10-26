<?php

namespace App\Models;

use App\Enums\ProductReviewPointSentimentEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_review_id
 * @property ProductReviewPointSentimentEnum $sentiment
 * @property string $body
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read ProductReview $review
 */
#[Guarded('id')]
class ProductReviewPoint extends Model
{
    use HasFactory;

    public function review(): BelongsTo
    {
        return $this->belongsTo(ProductReview::class, 'product_review_id');
    }

    protected function casts(): array
    {
        return [
            'sentiment' => ProductReviewPointSentimentEnum::class,
            'sort_order' => 'integer',
        ];
    }

    public static function deleteForReviewSentiment(ProductReview $review, ProductReviewPointSentimentEnum $sentiment): void
    {
        self::query()
            ->where('product_review_id', $review->id)
            ->where('sentiment', $sentiment->value)
            ->delete();
    }

    public static function createForReview(
        ProductReview $review,
        ProductReviewPointSentimentEnum $sentiment,
        string $body,
        int $sortOrder,
    ): self {
        $point = new self();
        $point->product_review_id = $review->id;
        $point->sentiment = $sentiment;
        $point->body = $body;
        $point->sort_order = $sortOrder;
        $point->save();

        return $point;
    }
}
