<?php

namespace App\Models;

use App\DTO\Specification\SpecificationAnswerData;
use App\Enums\SpecificationTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $item_id
 * @property int $specification_id
 * @property int|null $specification_option_id
 * @property string|null $value_text
 * @property string|null $value_number
 * @property bool|null $value_boolean
 * @property int $sort_order
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class ItemSpecificationValue extends Model
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(SpecificationOption::class, 'specification_option_id');
    }

    protected function casts(): array
    {
        return ['value_boolean' => 'boolean', 'sort_order' => 'integer'];
    }

    /** @param array<int, SpecificationAnswerData> $answers */
    public static function replaceForItem(Item $item, array $answers): void
    {
        $specifications = Specification::forCategory($item->category_id)->keyBy('id');
        $received = [];
        $rows = [];

        foreach ($answers as $answer) {
            $specification = $specifications->get($answer->specificationId);

            if (! $specification || isset($received[$answer->specificationId])) {
                throw ValidationException::withMessages(['specifications' => 'مشخصه انتخاب‌شده برای دسته‌بندی این آیتم معتبر نیست.']);
            }

            $received[$answer->specificationId] = true;
            $type = $specification->type;
            $optionIds = array_values(array_unique($answer->optionIds));
            $hasText = $answer->text !== null && trim($answer->text) !== '';
            $hasNumber = $answer->number !== null && $answer->number !== '';
            $hasBoolean = $answer->boolean !== null;

            if ($type === SpecificationTypeEnum::TEXT && $hasText) {
                $rows[] = ['specification_id' => $specification->id, 'value_text' => trim($answer->text)];
            } elseif ($type === SpecificationTypeEnum::NUMBER && $hasNumber) {
                $rows[] = ['specification_id' => $specification->id, 'value_number' => $answer->number];
            } elseif ($type === SpecificationTypeEnum::BOOLEAN && $hasBoolean) {
                $rows[] = ['specification_id' => $specification->id, 'value_boolean' => $answer->boolean];
            } elseif ($type->usesOptions() && $optionIds !== []) {
                if ($type === SpecificationTypeEnum::SELECT && count($optionIds) > 1) {
                    throw ValidationException::withMessages(['specifications' => 'برای مشخصه تک‌انتخابی فقط یک گزینه مجاز است.']);
                }

                $allowed = $specification->options->pluck('id')->all();

                if (array_diff($optionIds, $allowed) !== []) {
                    throw ValidationException::withMessages(['specifications' => 'یکی از گزینه‌های مشخصه معتبر نیست.']);
                }

                foreach ($optionIds as $position => $optionId) {
                    $rows[] = [
                        'specification_id' => $specification->id,
                        'specification_option_id' => $optionId,
                        'sort_order' => $position,
                    ];
                }
            } elseif ($specification->is_required) {
                throw ValidationException::withMessages(['specifications' => "مقدار مشخصه «{$specification->name}» الزامی است."]);
            }
        }

        foreach ($specifications as $specification) {
            if ($specification->is_required && ! isset($received[$specification->id])) {
                throw ValidationException::withMessages(['specifications' => "مقدار مشخصه «{$specification->name}» الزامی است."]);
            }
        }

        self::query()->where('item_id', $item->id)->delete();

        foreach ($rows as $row) {
            self::query()->create(['item_id' => $item->id, ...$row]);
        }
    }

    public function displayValue(): string
    {
        if ($this->option !== null) {
            return $this->option->label;
        }

        if ($this->value_boolean !== null) {
            return $this->value_boolean ? 'بله' : 'خیر';
        }

        if ($this->value_number !== null) {
            $number = str_contains($this->value_number, '.')
                ? rtrim(rtrim($this->value_number, '0'), '.')
                : $this->value_number;
            return $number . ($this->specification->unit ? ' ' . $this->specification->unit : '');
        }

        return $this->value_text ?? '';
    }
}
