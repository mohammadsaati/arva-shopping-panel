<?php

namespace App\Models;

use App\Enums\GlobalStatusEnum;
use App\Models\Concerns\FiltersFooterEntries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property int $sort_order
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class FooterLinkGroup extends Model
{
    use FiltersFooterEntries, HasFactory;

    public function links(): HasMany
    {
        return $this->hasMany(FooterLink::class)->orderBy('sort_order')->orderBy('id');
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    /** @return Collection<int, self> */
    public static function visibleWithLinks(): Collection
    {
        return self::query()->where('status', GlobalStatusEnum::ACTIVE->value)
            ->with(['links' => fn ($query) => $query->where('status', GlobalStatusEnum::ACTIVE->value)])
            ->orderBy('sort_order')->orderBy('id')->get();
    }

    /** @return Collection<int, self> */
    public static function adminAll(): Collection
    {
        return self::query()->orderBy('sort_order')->orderBy('id')->get();
    }

    public static function details(int $id): self
    {
        return self::query()->findOrFail($id);
    }

    /** @param array<string, mixed> $attributes */
    public static function createEntry(array $attributes): self
    {
        return self::query()->create($attributes);
    }

    /** @param array<string, mixed> $attributes */
    public function updateEntry(array $attributes): void
    {
        $this->fill($attributes)->save();
    }

    public function deleteEntry(): void
    {
        $this->delete();
    }

    public function hasLinks(): bool
    {
        return $this->links()->exists();
    }
}
