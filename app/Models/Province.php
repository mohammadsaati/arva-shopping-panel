<?php

namespace App\Models;

use App\DTO\Province\ProvinceCreateData;
use App\DTO\Province\ProvinceFilterData;
use App\DTO\Province\ProvinceUpdateData;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Province extends Model
{
    use HasFactory;

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, self> */
    public static function activeWithCities(): \Illuminate\Database\Eloquent\Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->with([
                'cities' => fn (HasMany $cities) => $cities
                    ->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->orderBy('name'),
            ])
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public static function filter(ProvinceFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->withCount('cities')
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $builder->where('name', 'like', "%{$filters->search}%");
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function create(ProvinceCreateData $data): self
    {
        $province = new self();
        $province->name = $data->name;
        $province->status = $data->status;
        $province->save();

        return $province;
    }

    public static function details(int $provinceId): self
    {
        return self::query()->withCount('cities')->findOrFail($provinceId);
    }

    public function updateDetails(ProvinceUpdateData $data): self
    {
        foreach ($data->toModelAttributes() as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        $this->save();

        return $this;
    }

    /** @return array<int, string> */
    public static function options(): array
    {
        return self::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
