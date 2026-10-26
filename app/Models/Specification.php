<?php

namespace App\Models;

use App\DTO\Specification\SpecificationData;
use App\Enums\SpecificationTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $specification_group_id
 * @property string $key
 * @property string $name
 * @property SpecificationTypeEnum $type
 * @property string|null $unit
 * @property bool $is_required
 * @property bool $is_highlighted
 * @property bool $is_filterable
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Specification extends Model
{
    public function group(): BelongsTo
    {
        return $this->belongsTo(SpecificationGroup::class, 'specification_group_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(SpecificationOption::class)->orderBy('sort_order')->orderBy('id');
    }

    public function itemValues(): HasMany
    {
        return $this->hasMany(ItemSpecificationValue::class);
    }

    protected function casts(): array
    {
        return [
            'type' => SpecificationTypeEnum::class,
            'is_required' => 'boolean',
            'is_highlighted' => 'boolean',
            'is_filterable' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /** @return Collection<int, self> */
    public static function forCategory(int $categoryId): Collection
    {
        return self::query()->whereHas('group', fn ($group) => $group->where('category_id', $categoryId))
            ->with(['group', 'options'])->get();
    }

    public static function detailsForCategory(int $categoryId, int $specificationId): self
    {
        return self::query()->whereHas('group', fn ($group) => $group->where('category_id', $categoryId))
            ->with(['group', 'options'])->findOrFail($specificationId);
    }

    /** @param array<string, string> $options */
    public static function createForCategory(int $categoryId, SpecificationData $data, array $options): self
    {
        return DB::transaction(function () use ($categoryId, $data, $options): self {
            SpecificationGroup::query()->where('category_id', $categoryId)->findOrFail($data->groupId);
            $specification = self::query()->create(self::attributesFrom($data));
            SpecificationOption::syncForSpecification($specification, $options);

            return $specification;
        });
    }

    /** @param array<string, string> $options */
    public static function updateForCategory(int $categoryId, int $specificationId, SpecificationData $data, array $options): self
    {
        return DB::transaction(function () use ($categoryId, $specificationId, $data, $options): self {
            SpecificationGroup::query()->where('category_id', $categoryId)->findOrFail($data->groupId);
            $specification = self::query()->whereHas('group', fn ($group) => $group->where('category_id', $categoryId))
                ->findOrFail($specificationId);

            if ($specification->type !== $data->type && $specification->itemValues()->exists()) {
                throw ValidationException::withMessages(['type' => 'نوع مشخصه پس از ثبت مقدار برای آیتم‌ها قابل تغییر نیست.']);
            }

            $specification->update(self::attributesFrom($data));
            SpecificationOption::syncForSpecification($specification, $options);

            return $specification;
        });
    }

    public static function deleteForCategory(int $categoryId, int $specificationId): void
    {
        $specification = self::query()->whereHas('group', fn ($group) => $group->where('category_id', $categoryId))
            ->findOrFail($specificationId);

        if ($specification->itemValues()->exists()) {
            throw ValidationException::withMessages(['specification' => 'این مشخصه برای آیتم‌ها مقدار دارد و قابل حذف نیست.']);
        }

        $specification->delete();
    }

    /** @return array<string, mixed> */
    private static function attributesFrom(SpecificationData $data): array
    {
        return [
            'specification_group_id' => $data->groupId,
            'key' => $data->key,
            'name' => $data->name,
            'type' => $data->type,
            'unit' => $data->unit,
            'is_required' => $data->isRequired,
            'is_highlighted' => $data->isHighlighted,
            'is_filterable' => $data->isFilterable,
            'sort_order' => $data->sortOrder,
        ];
    }
}
