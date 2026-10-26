<?php

namespace App\Models;

use App\DTO\City\CityCreateData;
use App\DTO\City\CityFilterData;
use App\DTO\City\CityUpdateData;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $province_id
 * @property string $name
 * @property string $slug
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Province $province
 */
#[Guarded('id')]
class City extends Model
{
    use HasFactory;

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    public static function filter(CityFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'slug', 'province_id', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with('province')
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('province', fn (Builder $province) => $province->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($filters->provinceId !== null, fn (Builder $builder) => $builder->where('province_id', $filters->provinceId))
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function create(CityCreateData $data): self
    {
        $city = new self();
        $city->province_id = $data->provinceId;
        $city->name = $data->name;
        $city->slug = $data->slug;
        $city->status = $data->status;
        $city->save();

        return $city;
    }

    public static function details(int $cityId): self
    {
        return self::query()->with('province')->findOrFail($cityId);
    }

    public function updateDetails(CityUpdateData $data): self
    {
        foreach ($data->toModelAttributes() as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        $this->save();

        return $this;
    }

    /** @return array<string, array<int, string>> */
    public static function shippingOptions(): array
    {
        return self::query()
            ->with('province')
            ->orderBy('name')
            ->get()
            ->groupBy(fn (self $city): string => $city->province->name)
            ->map(fn ($cities): array => $cities->pluck('name', 'id')->all())
            ->all();
    }
}
