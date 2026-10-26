<?php

namespace App\Models;

use App\Enums\GlobalStatusEnum;
use App\Models\Concerns\FiltersFooterEntries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $label
 * @property string|null $image_path
 * @property string $url
 * @property int $sort_order
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class FooterAppLink extends Model
{
    use FiltersFooterEntries, HasFactory;

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    /** @return Collection<int, self> */
    public static function visible(): Collection
    {
        return self::query()->where('status', GlobalStatusEnum::ACTIVE->value)->orderBy('sort_order')->orderBy('id')->get();
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
}
