<?php

namespace App\Models;

use App\DTO\NewsletterSubscriberFilterData;
use App\DTO\NewsletterSubscriberStatusData;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property GlobalStatusEnum $status
 * @property Carbon $subscribed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class NewsletterSubscriber extends Model
{
    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class, 'subscribed_at' => 'datetime'];
    }

    public static function subscribe(string $email): self
    {
        return self::query()->updateOrCreate(
            ['email' => mb_strtolower(trim($email))],
            ['status' => GlobalStatusEnum::ACTIVE, 'subscribed_at' => now()],
        );
    }

    public static function filter(NewsletterSubscriberFilterData $filters): Paginator
    {
        $sorts = array_intersect_key($filters->sorts, array_flip(['id', 'email', 'status', 'subscribed_at', 'created_at']));
        $query = self::query()
            ->when($filters->search !== null && $filters->search !== '', fn (Builder $query) => $query->where('email', 'like', '%'.$filters->search.'%'))
            ->when($filters->status !== null, fn (Builder $query) => $query->where('status', $filters->status->value));

        foreach ($sorts ?: ['subscribed_at' => 'desc'] as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->orderBy('id')->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function updateStatus(int $id, NewsletterSubscriberStatusData $data): void
    {
        $subscriber = self::query()->findOrFail($id);
        $subscriber->status = $data->status;
        $subscriber->save();
    }
}
