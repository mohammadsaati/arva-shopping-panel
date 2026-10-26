<?php

namespace App\Models;

use App\DTO\Color\ColorCreateData;
use App\DTO\Color\ColorFilterData;
use App\DTO\Color\ColorUpdateData;
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
 * @property string $value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Color extends Model
{
    use HasFactory;

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function filter(ColorFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'value', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('value', 'like', "%{$search}%");
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function create(ColorCreateData $data): self
    {
        $color = new self();
        $color->name = $data->name;
        $color->value = $data->value;
        $color->save();

        return $color;
    }

    public static function details(int $colorId): self
    {
        return self::query()->findOrFail($colorId);
    }

    public function updateDetails(ColorUpdateData $data): self
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
        return self::query()
            ->orderBy('name')
            ->get(['id', 'name', 'value'])
            ->mapWithKeys(fn (self $color) => [$color->id => "{$color->name} ({$color->value})"])
            ->all();
    }
}
