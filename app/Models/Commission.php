<?php

namespace App\Models;

use App\DTO\Commission\CommissionFilterData;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property int $rate_bps
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Vendor> $vendors
 * @property-read int $vendors_count
 */
#[Guarded('id')]
class Commission extends Model
{
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    protected function casts(): array
    {
        return ['rate_bps' => 'integer'];
    }

    public static function filter(CommissionFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'title', 'rate_bps', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->withCount('vendors')
            ->when($filters->search !== null && trim($filters->search) !== '', fn (Builder $builder) => $builder
                ->where('title', 'like', '%' . trim($filters->search) . '%'));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    /** @return array<int, string> */
    public static function selectionOptions(): array
    {
        return self::query()->orderBy('title')->get()
            ->mapWithKeys(fn (self $commission): array => [
                $commission->id => $commission->title . ' (' . number_format($commission->rate_bps / 100, 2) . '٪)',
            ])->all();
    }

    public static function existsId(int $commissionId): bool
    {
        return self::query()->whereKey($commissionId)->exists();
    }

    public static function details(int $commissionId): self
    {
        return self::query()->findOrFail($commissionId);
    }

    public static function createDefinition(string $title, int $rateBps): self
    {
        $commission = new self();
        $commission->title = $title;
        $commission->rate_bps = $rateBps;
        $commission->save();

        return $commission;
    }

    public function updateDefinition(?string $title, ?int $rateBps): self
    {
        if ($title !== null) {
            $this->title = $title;
        }

        if ($rateBps !== null) {
            $this->rate_bps = $rateBps;
        }

        if ($this->isDirty()) {
            $this->save();
        }

        return $this;
    }
}
