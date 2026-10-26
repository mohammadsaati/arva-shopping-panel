<?php

namespace App\Models;

use App\DTO\Size\SizeCreateData;
use App\DTO\Size\SizeFilterData;
use App\DTO\Size\SizeUpdateData;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Size extends Model
{
    use HasFactory;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function filter(SizeFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'value', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->when($filters->search !== null && $filters->search !== '', fn (Builder $builder) => $builder->where('value', 'like', "%{$filters->search}%"));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function create(SizeCreateData $data): self
    {
        $size = new self();
        $size->value = $data->value;
        $size->save();

        return $size;
    }

    public static function details(int $sizeId): self
    {
        return self::query()->findOrFail($sizeId);
    }

    public function updateDetails(SizeUpdateData $data): self
    {
        $attributes = $data->toModelAttributes();

        if ($attributes !== []) {
            foreach ($attributes as $attribute => $value) {
                $this->setAttribute($attribute, $value);
            }

            $this->save();
        }

        return $this;
    }

    /** @return array<int, string> */
    public static function options(): array
    {
        return self::query()->orderBy('value')->pluck('value', 'id')->all();
    }
}
