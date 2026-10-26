<?php

namespace App\Models;

use App\DTO\ProductReview\ProductReviewCreateData;
use App\DTO\ProductReview\ProductReviewFilterData;
use App\DTO\ProductReview\ProductReviewPublicFilterData;
use App\DTO\ProductReview\ProductReviewUpdateData;
use App\Enums\ProductReviewApprovalStatusEnum;
use App\Enums\ProductReviewPointSentimentEnum;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int $item_id
 * @property int|null $user_id
 * @property string|null $guest_name
 * @property string|null $guest_email
 * @property int $rating
 * @property string|null $title
 * @property string $body
 * @property ProductReviewApprovalStatusEnum $approval_status
 * @property int|null $approved_by_admin_id
 * @property Carbon|null $approved_at
 * @property int|null $rejected_by_admin_id
 * @property Carbon|null $rejected_at
 * @property Carbon|null $verified_purchase_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Item $item
 * @property-read User|null $user
 * @property-read Admin|null $approvedByAdmin
 * @property-read Admin|null $rejectedByAdmin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductReviewPoint> $points
 */
#[Guarded('id')]
class ProductReview extends Model
{
    use HasFactory;

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by_admin_id');
    }

    public function rejectedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'rejected_by_admin_id');
    }

    public function points(): HasMany
    {
        return $this->hasMany(ProductReviewPoint::class)->orderBy('sentiment')->orderBy('sort_order')->orderBy('id');
    }

    public function scopeApproved(Builder $builder): void
    {
        $builder->where('approval_status', ProductReviewApprovalStatusEnum::APPROVED->value);
    }

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'approval_status' => ProductReviewApprovalStatusEnum::class,
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'verified_purchase_at' => 'datetime',
        ];
    }

    /** @return Paginator<ProductReview> */
    public static function publicForItem(ProductReviewPublicFilterData $filters): Paginator
    {
        $allowedSorts = ['approved_at', 'id'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['approved_at' => 'desc'];
        $query = self::query()
            ->approved()
            ->where('item_id', $filters->itemId)
            ->with(['user', 'points']);

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        if (! array_key_exists('id', $sorts)) {
            $query->orderByDesc('id');
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    /**
     * @return array{count: int, average: float, distribution: array<int, int>}
     */
    public static function publicSummaryForItem(int $itemId): array
    {
        $ratingCounts = self::query()
            ->approved()
            ->where('item_id', $itemId)
            ->select('rating')
            ->selectRaw('COUNT(*) as review_count')
            ->groupBy('rating')
            ->get();

        $distribution = array_fill(1, 5, 0);
        foreach ($ratingCounts as $ratingCount) {
            $distribution[(int) $ratingCount->rating] = (int) $ratingCount->review_count;
        }

        $count = array_sum($distribution);
        $weightedTotal = 0;
        foreach ($distribution as $rating => $ratingCount) {
            $weightedTotal += $rating * $ratingCount;
        }

        return [
            'count' => $count,
            'average' => $count > 0 ? round($weightedTotal / $count, 1) : 0.0,
            'distribution' => $distribution,
        ];
    }

    public static function filterForAdmin(ProductReviewFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'rating', 'approval_status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];
        $search = trim((string) $filters->search);

        $query = self::query()
            ->with(['item', 'user'])
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('approval_status', $filters->status->value))
            ->when($search !== '', function (Builder $builder) use ($search): void {
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('title', 'like', "%{$search}%")
                        ->orWhere('body', 'like', "%{$search}%")
                        ->orWhere('guest_name', 'like', "%{$search}%")
                        ->orWhere('guest_email', 'like', "%{$search}%")
                        ->orWhereHas('item', fn (Builder $item) => $item->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('user', function (Builder $user) use ($search): void {
                            $user->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });

                    if (ctype_digit($search)) {
                        $builder->orWhereKey((int) $search);
                    }
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function detailsForAdmin(int $reviewId): self
    {
        return self::query()
            ->with(['item', 'user', 'points', 'approvedByAdmin', 'rejectedByAdmin'])
            ->findOrFail($reviewId);
    }

    public static function createFromStorefront(
        Item $item,
        ?User $user,
        ProductReviewCreateData $data,
        ProductReviewApprovalStatusEnum $initialStatus,
        bool $verifiedPurchase,
    ): self {
        return DB::transaction(function () use ($item, $user, $data, $initialStatus, $verifiedPurchase): self {
            $review = new self();
            $review->item_id = $item->id;
            $review->user_id = $user?->id;
            $review->guest_name = $user === null ? $data->guestName : null;
            $review->guest_email = $user === null ? $data->guestEmail : null;
            $review->rating = $data->rating;
            $review->title = $data->title;
            $review->body = $data->body;
            $review->approval_status = $initialStatus;
            $review->verified_purchase_at = $verifiedPurchase ? now() : null;
            $review->save();

            $review->replacePoints(ProductReviewPointSentimentEnum::POSITIVE, $data->positivePoints);
            $review->replacePoints(ProductReviewPointSentimentEnum::NEGATIVE, $data->negativePoints);

            return $review;
        });
    }

    public function approveByAdmin(int $adminId): void
    {
        $this->approval_status = ProductReviewApprovalStatusEnum::APPROVED;
        $this->approved_by_admin_id = $adminId;
        $this->approved_at = now();
        $this->rejected_by_admin_id = null;
        $this->rejected_at = null;
        $this->save();
    }

    public function rejectByAdmin(int $adminId): void
    {
        $this->approval_status = ProductReviewApprovalStatusEnum::REJECTED;
        $this->approved_by_admin_id = null;
        $this->approved_at = null;
        $this->rejected_by_admin_id = $adminId;
        $this->rejected_at = now();
        $this->save();
    }

    public function returnToPending(): void
    {
        $this->approval_status = ProductReviewApprovalStatusEnum::PENDING;
        $this->approved_by_admin_id = null;
        $this->approved_at = null;
        $this->rejected_by_admin_id = null;
        $this->rejected_at = null;
        $this->save();
    }

    /** @param list<string> $positivePoints @param list<string> $negativePoints */
    public function replacePointsByAdmin(array $positivePoints, array $negativePoints): void
    {
        $this->replacePoints(ProductReviewPointSentimentEnum::POSITIVE, $positivePoints);
        $this->replacePoints(ProductReviewPointSentimentEnum::NEGATIVE, $negativePoints);
    }

    public static function pendingCount(): int
    {
        return self::query()
            ->where('approval_status', ProductReviewApprovalStatusEnum::PENDING->value)
            ->count();
    }

    public static function dashboardPending(int $limit): Paginator
    {
        return self::query()
            ->where('approval_status', ProductReviewApprovalStatusEnum::PENDING->value)
            ->with(['item', 'user'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->simplePaginate(perPage: $limit, page: 1)
            ->withQueryString();
    }

    public static function runInTransaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }

    /** @param list<string> $bodies */
    private function replacePoints(ProductReviewPointSentimentEnum $sentiment, array $bodies): void
    {
        ProductReviewPoint::deleteForReviewSentiment($this, $sentiment);

        foreach (array_slice($bodies, 0, 10) as $sortOrder => $body) {
            $body = trim($body);
            if ($body !== '') {
                ProductReviewPoint::createForReview($this, $sentiment, $body, $sortOrder);
            }
        }
    }
}
