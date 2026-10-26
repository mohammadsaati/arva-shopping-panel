<?php

namespace App\Models;

use App\DTO\Brand\BrandCreateData;
use App\DTO\Brand\BrandFilterData;
use App\DTO\Brand\BrandUpdateData;
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
 * @property string $slug
 * @property string|null $image
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Brand extends Model
{
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    public static function filter(BrandFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'slug', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->withCount('items')
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function create(BrandCreateData $data, ?string $imagePath): self
    {
        $brand = new self();
        $brand->name = $data->name;
        $brand->slug = $data->slug;
        $brand->image = $imagePath;
        $brand->status = $data->status;
        $brand->save();

        return $brand;
    }

    public static function details(int $brandId): self
    {
        return self::query()->findOrFail($brandId);
    }

    public function updateDetails(BrandUpdateData $data, ?string $imagePath): self
    {
        foreach ($data->toModelAttributes() as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        if ($imagePath !== null) {
            $this->image = $imagePath;
        }

        $this->save();

        return $this;
    }

    /** @return array<int, string> */
    public static function activeOptions(?int $includeBrandId = null): array
    {
        return self::query()
            ->where(function (Builder $builder) use ($includeBrandId): void {
                $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->when($includeBrandId !== null, fn (Builder $builder) => $builder->orWhereKey($includeBrandId));
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
