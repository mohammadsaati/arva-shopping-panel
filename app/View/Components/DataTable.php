<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class DataTable extends Component
{
    public string $tableId;

    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        ?string $id = null,
    ) {
        $this->tableId = $id ?? 'dt_'.Str::random(8);
    }

    public static function normalizeColumn(array $col): array
    {
        $normalized = array_merge([
            'key' => '',
            'sortable' => false,
            'html' => false,
            'class' => '',
            'format' => null,
            'prefix' => null,
            'suffix' => null,
            'empty' => '-',
            'badge' => null,
            'conditions' => [],
            'view' => null,
        ], $col);

        if (! empty($col['view'])) {
            $normalized['html'] = true;
        }

        return $normalized;
    }

    public static function resolveCell(mixed $item, array $col): string
    {
        $raw = data_get($item, $col['key']);

        if ($col['html']) {
            if ($col['view']) {
                return view($col['view'], ['item' => $item, 'row' => $item])->render();
            }

            return (string) ($raw ?? '');
        }

        $display = self::formatValue($raw, $col);

        if ($col['badge'] !== null && $raw !== null) {
            $badgeClass = $col['badge'][strval($raw)] ?? $col['badge']['_default'] ?? '';

            return '<span class="badge '.e($badgeClass).'">'.e($display).'</span>';
        }

        return e($display);
    }

    public static function resolveCellClass(mixed $item, array $col): string
    {
        $classes = ['px-5 py-3 text-sm text-gray-700'];

        if ($col['class']) {
            $classes[] = $col['class'];
        }

        foreach ($col['conditions'] as $cond) {
            if (self::evalCondition($item, $cond)) {
                $classes[] = $cond['class'];
                break;
            }
        }

        return implode(' ', $classes);
    }

    public static function formatValue(mixed $value, array $col): string
    {
        if ($value === null || $value === '') {
            return $col['empty'];
        }

        $out = match ($col['format']) {
            'date' => jalali_date($value) ?? (string) $value,
            'datetime' => jalali_date($value, 'Y/m/d H:i') ?? (string) $value,
            'number' => number_format((float) $value),
            'currency' => number_format((float) $value),
            'boolean' => $value ? 'بله' : 'خیر',
            default => (string) $value,
        };

        if ($col['prefix']) {
            $out = $col['prefix'].$out;
        }
        if ($col['suffix']) {
            $out = $out.$col['suffix'];
        }

        return $out;
    }

    public static function evalCondition(mixed $item, array $cond): bool
    {
        $field = $cond['field'] ?? $cond['key'] ?? '';
        $actual = strval(data_get($item, $field) ?? '');
        $expect = strval($cond['value'] ?? '');

        return match ($cond['op'] ?? 'eq') {
            'eq' => $actual === $expect,
            'neq' => $actual !== $expect,
            'gt' => (float) $actual > (float) $expect,
            'gte' => (float) $actual >= (float) $expect,
            'lt' => (float) $actual < (float) $expect,
            'lte' => (float) $actual <= (float) $expect,
            'contains' => str_contains($actual, $expect),
            'starts' => str_starts_with($actual, $expect),
            'ends' => str_ends_with($actual, $expect),
            'truthy' => (bool) data_get($item, $field),
            'falsy' => ! (bool) data_get($item, $field),
            default => false,
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.datatable');
    }
}
