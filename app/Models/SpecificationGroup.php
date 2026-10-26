<?php

namespace App\Models;

use App\DTO\Specification\SpecificationGroupData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class SpecificationGroup extends Model
{
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function specifications(): HasMany
    {
        return $this->hasMany(Specification::class)->orderBy('sort_order')->orderBy('id');
    }

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /** @return Collection<int, self> */
    public static function forCategory(int $categoryId): Collection
    {
        return self::query()->where('category_id', $categoryId)
            ->with('specifications.options')
            ->orderBy('sort_order')->orderBy('id')->get();
    }

    public static function createForCategory(int $categoryId, SpecificationGroupData $data): self
    {
        Category::query()->findOrFail($categoryId);

        return self::query()->create([
            'category_id' => $categoryId,
            'name' => $data->name,
            'sort_order' => $data->sortOrder,
        ]);
    }

    public static function updateForCategory(int $categoryId, int $groupId, SpecificationGroupData $data): self
    {
        $group = self::query()->where('category_id', $categoryId)->findOrFail($groupId);
        $group->update(['name' => $data->name, 'sort_order' => $data->sortOrder]);

        return $group;
    }

    public static function deleteForCategory(int $categoryId, int $groupId): void
    {
        $group = self::query()->where('category_id', $categoryId)->findOrFail($groupId);

        if ($group->specifications()->exists()) {
            throw ValidationException::withMessages(['group' => 'ابتدا مشخصه‌های این گروه را حذف کنید.']);
        }

        $group->delete();
    }
}
