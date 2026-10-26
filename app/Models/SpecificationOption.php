<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $specification_id
 * @property string $value
 * @property string $label
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class SpecificationOption extends Model
{
    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class);
    }

    public function itemValues(): HasMany
    {
        return $this->hasMany(ItemSpecificationValue::class);
    }

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    /** @param array<string, string> $options */
    public static function syncForSpecification(Specification $specification, array $options): void
    {
        DB::transaction(function () use ($specification, $options): void {
            $existing = self::query()->where('specification_id', $specification->id)->get()->keyBy('value');

            $position = 0;
            foreach ($options as $value => $label) {
                $option = $existing->get($value);

                if ($option) {
                    $option->update(['label' => $label, 'sort_order' => $position]);
                } else {
                    self::query()->create([
                        'specification_id' => $specification->id,
                        'value' => $value,
                        'label' => $label,
                        'sort_order' => $position,
                    ]);
                }
                $position++;
            }

            foreach ($existing as $value => $option) {
                if (! array_key_exists($value, $options)) {
                    if ($option->itemValues()->exists()) {
                        throw ValidationException::withMessages(['options' => "گزینه «{$option->label}» برای آیتم‌ها استفاده شده و قابل حذف نیست."]);
                    }

                    $option->delete();
                }
            }
        });
    }
}
