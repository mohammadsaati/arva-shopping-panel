<?php

namespace App\Models;

use App\Enums\GlobalStatusEnum;
use App\Models\Concerns\FiltersFooterEntries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $footer_link_group_id
 * @property string $label
 * @property string $url
 * @property bool $opens_in_new_tab
 * @property int $sort_order
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class FooterLink extends Model
{
    use FiltersFooterEntries, HasFactory;

    public function group(): BelongsTo
    {
        return $this->belongsTo(FooterLinkGroup::class, 'footer_link_group_id');
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class, 'opens_in_new_tab' => 'boolean'];
    }

    /** @return Collection<int, self> */
    public static function adminAll(): Collection
    {
        return self::query()->with('group')->orderBy('footer_link_group_id')->orderBy('sort_order')->orderBy('id')->get();
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
